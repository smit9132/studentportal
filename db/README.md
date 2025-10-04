# Database schema for StudentPortal

This folder contains the SQL schema and helper files for the StudentPortal application (designed for MySQL / MariaDB shipped with XAMPP).

Files
- `schema.sql` - Full schema: creates the `studentportal` database and required tables, plus a default admin account (for development only).
- `migration.sql` - Optional non-destructive ALTER statements to update an existing database.

Quick import (using MySQL CLI or phpMyAdmin):

1) From command line (Windows PowerShell):

```powershell
mysql -u root -p < schema.sql
```

2) Or open `schema.sql` in phpMyAdmin and run the SQL.

Notes and recommendations
- The default admin account uses a bcrypt hash for the example password. Replace or delete this account in production.
- The schema uses `utf8mb4` and `InnoDB` to support modern Unicode and foreign keys.
- Back up your database before applying `migration.sql`.

Web setup helper
- `setup.php` is a small local helper to create or update an admin user from the browser: `http://localhost/StudentPortal/db/setup.php`.
- This script uses prepared statements and `password_hash`. After running it once, delete `db/setup.php` from the server to avoid leaving a setup endpoint in production.
