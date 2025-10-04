<#
PowerShell helper to create a GitHub repository and push the current project.
Usage:
  - If you have `gh` (GitHub CLI) installed and authenticated, the script will use it.
  - Otherwise provide a GITHUB_TOKEN environment variable (Personal Access Token with repo scope).

Example:
  $env:GITHUB_TOKEN = 'ghp_...'
  .\scripts\create_github_repo.ps1 -RepoName StudentPortal -Description "StudentPortal web app" -Private:$false
#>
param(
    [Parameter(Mandatory=$true)]
    [string]$RepoName,
    [string]$Description = "",
    [switch]$Private = $false
)

function Exec-Git([string]$cmd) {
    Write-Host "> git $cmd"
    git $cmd
}

# If gh CLI available, use it
if (Get-Command gh -ErrorAction SilentlyContinue) {
    Write-Host "Using gh CLI to create repository..."
    $privateFlag = $Private.IsPresent ? "--private" : "--public"
    gh repo create $RepoName --description "$Description" $privateFlag --source=. --remote=origin --push
    if ($LASTEXITCODE -ne 0) { Write-Error "gh repo create failed"; exit 1 }
    Write-Host "Repository created and pushed via gh.";
    exit 0
}

# Fallback: use GitHub API with PAT
$token = $env:GITHUB_TOKEN
if (-not $token) {
    Write-Error "GitHub CLI not found and GITHUB_TOKEN not set. Install gh or set GITHUB_TOKEN environment variable with 'repo' scope."
    exit 1
}

$body = @{ name = $RepoName; description = $Description; private = ($Private.IsPresent) } | ConvertTo-Json
$headers = @{ Authorization = "token $token"; Accept = 'application/vnd.github.v3+json' }

$apiUrl = 'https://api.github.com/user/repos'
$response = Invoke-RestMethod -Method Post -Uri $apiUrl -Headers $headers -Body $body
if ($response -and $response.ssh_url) {
    Write-Host "Created repo: $($response.full_name)"
    # Initialize git if needed
    if (-not (Test-Path .git)) {
        git init
    }
    if (-not (git remote get-url origin 2>$null)) {
        git remote add origin $response.ssh_url
    }
    Exec-Git "add ."
    Exec-Git "commit -m 'Initial commit'"
    Exec-Git "branch -M main"
    Exec-Git "push -u origin main"
    Write-Host "Pushed to $($response.ssh_url)"
} else {
    Write-Error "Failed to create repository via API"
    exit 1
}
