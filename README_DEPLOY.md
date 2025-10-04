# Deployment guide — StudentPortal

This guide explains how to push this project to GitHub and deploy to a free hosting provider (Render / Railway / Fly / Vercel for static parts). StudentPortal is a PHP app that requires a MySQL-compatible database.

Quick steps

1. Create a new GitHub repository and push the project:

```powershell
cd C:\xampp\htdocs\StudentPortal
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/<your-user>/<your-repo>.git
git push -u origin main
```

2. Choose a host for PHP + MySQL. Options:
- Render (free tier): create a Web Service with Docker (connect to a managed Postgres/MySQL instance or external DB provider).
- Railway / Neon / PlanetScale / ClearDB: managed MySQL-compatible DB. Use environment variables.

Render deployment (Docker)
- Create a new Web Service on Render.
- Connect your GitHub repo.
- Set Build Command: `docker build -t studentportal .`
- Set Start Command: `docker run -p 80:80 studentportal` (Render will handle the container run automatically).
- Add environment variables (in Render dashboard):
  - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`

Database
- Use a managed MySQL service (Planetscale requires some topology adjustments; if using PlanetScale, use a deploy request for schema changes).
- Alternatively, host a small MySQL instance on Render (Add-on) or use ClearDB.

Local testing
- Use XAMPP for quick local testing. Use `db/init_db.php` or `php db\init_db.php` to import schema.

Security
- Remove `db/setup.php` after creating your admin user.
- Store secrets in Render / GitHub Secrets, not in repo.

If you'd like, I can:
- Create a GitHub repo and push for you (you'll need to provide access token).
- Prepare a Render service manifest or Docker Compose for a single-click deploy.

Automated GitHub repo creation (optional)
---------------------------------------
There's a small PowerShell helper included at `scripts/create_github_repo.ps1` that will create a GitHub repository and push the current project.

Usage (PowerShell):

1) If you have the GitHub CLI (`gh`) installed and authenticated, the script will use it automatically:

```powershell
.\scripts\create_github_repo.ps1 -RepoName StudentPortal -Description "StudentPortal web app" -Private:$false
```

2) Otherwise set a Personal Access Token in the environment with `repo` scope and run the script:

```powershell
$env:GITHUB_TOKEN = "ghp_..."
.\scripts\create_github_repo.ps1 -RepoName StudentPortal -Description "StudentPortal web app" -Private:$false
```

The script will initialize git (if needed), create the remote repo, and push the code to `main`.
