# Deployment Guide

## Prerequisites

- PHP ^8.1
- MySQL 8.0+
- Composer 2.x
- Node.js & NPM (for frontend assets)

## Environment Configuration

### Required `.env` values for production:

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_DOMAIN=yourdomain.com        # Used for subdomain tenant resolution

DB_HOST=your-db-host
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

QUEUE_CONNECTION=database        # Required for media conversions
SESSION_DRIVER=database          # Required for multi-server
CACHE_DRIVER=redis               # Recommended for performance

MEDIA_DISK=s3                    # Or 'public' for single-server
```

### Tenant Subdomain Resolution

For subdomain-based tenant access to work (`{slug}.yourdomain.com`):
1. Set `APP_DOMAIN=yourdomain.com` in `.env`
2. Configure DNS wildcard `*.yourdomain.com` → your server IP
3. Configure your web server to accept all subdomains

**Nginx example:**
```nginx
server_name yourdomain.com *.yourdomain.com;
```

**Apache example:**
```apache
ServerAlias yourdomain.com *.yourdomain.com
```

### Without Subdomains (Main Domain Only)

Users can still access via the main domain:
- Log in at `https://yourdomain.com/login`
- After login, session stores the tenant context
- Tenant switching available via admin panel

## Migration & Seeding

```bash
php artisan migrate
php artisan db:seed --class=PermissionsTableSeeder
php artisan db:seed --class=PlanSeeder
php artisan storage:link
php artisan media-library:regenerate   # If existing media files
```

## Queue Worker

```bash
php artisan queue:work --queue=default --sleep=3 --tries=3
```

For production, use Supervisor to keep the worker running.

## Super Admin Access

URL: `https://superadmin.yourdomain.com` (if subdomain configured) or via main domain

Default super admin: `superadmin@admin.com` (password set during seeding)

## File Storage

### Local (Single Server)
Files stored in `storage/app/public/tenant/{slug}/`.

### S3 (Multi-Server)
Set `MEDIA_DISK=s3` and configure AWS credentials in `.env`.

## Security Checklist

- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] Strong `APP_KEY` generated
- [ ] HTTPS enforced
- [ ] Database connection uses strong password
- [ ] Session driver not `file` for multi-server
- [ ] Queue driver not `sync` for production
- [ ] SMTP credentials secured (not in repo)
- [ ] Regular backups configured
