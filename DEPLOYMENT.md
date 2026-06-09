# Astrokart — Production Deployment Guide

## Overview

Astrokart is a Vedic astrology platform with three components:
1. **Laravel web app** (PHP 8.3+) — main application
2. **Python microservice** (FastAPI) — horoscope calculations
3. **Reverb WebSocket server** — real-time chat (required only when astrologer feature is enabled)

---

## Prerequisites

| Component | Version |
|---|---|
| OS | Ubuntu 22.04 / 24.04 LTS (recommended) |
| PHP | 8.3+ (8.4 recommended) |
| Composer | 2.x |
| Node.js | 20+ (LTS) |
| npm | 10+ |
| Docker & Docker Compose | Latest |
| MySQL / MariaDB / PostgreSQL | MySQL 8.0+, MariaDB 10.5+, or PostgreSQL 13+ |
| Redis | 7.x |
| Nginx | 1.24+ |
| Supervisor | 4.x |

---

## Step 0: Server Setup (Ubuntu 22.04/24.04)

Start with a fresh Ubuntu server. These instructions assume root or sudo access.

### 0.1 System Update

```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common curl wget git unzip zip acl
```

### 0.2 Install PHP 8.4

```bash
# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP and required extensions
sudo apt install -y php8.4-fpm php8.4-cli php8.4-common \
    php8.4-mysql php8.4-pgsql php8.4-sqlite3 \
    php8.4-bcmath php8.4-ctype php8.4-curl php8.4-dom \
    php8.4-fileinfo php8.4-mbstring php8.4-xml php8.4-zip \
    php8.4-gd php8.4-intl php8.4-readline php8.4-redis \
    php8.4-tokenizer

# Verify
php -v
# PHP 8.4.x

# Configure PHP-FPM for production
sudo sed -i 's/upload_max_filesize = 2M/upload_max_filesize = 10M/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/post_max_size = 8M/post_max_size = 12M/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/memory_limit = 128M/memory_limit = 256M/' /etc/php/8.4/fpm/php.ini
sudo sed -i 's/max_execution_time = 30/max_execution_time = 120/' /etc/php/8.4/fpm/php.ini

sudo systemctl restart php8.4-fpm
sudo systemctl enable php8.4-fpm
```

### 0.3 Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### 0.4 Install Node.js 20 LTS

```bash
# Using NodeSource
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verify
node -v   # v20.x
npm -v    # 10.x
```

### 0.5 Install MySQL 8.0

```bash
sudo apt install -y mysql-server

# Secure installation
sudo mysql_secure_installation
# - Set root password
# - Remove anonymous users: Y
# - Disallow root login remotely: Y
# - Remove test database: Y
# - Reload privilege tables: Y

# Create database and user
sudo mysql -u root -p <<EOF
CREATE DATABASE astrokart CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'astrokart'@'localhost' IDENTIFIED BY 'your-strong-password';
GRANT ALL PRIVILEGES ON astrokart.* TO 'astrokart'@'localhost';
FLUSH PRIVILEGES;
EOF

sudo systemctl enable mysql
```

**Alternative: PostgreSQL 16**
```bash
sudo apt install -y postgresql postgresql-contrib

sudo -u postgres psql <<EOF
CREATE USER astrokart WITH PASSWORD 'your-strong-password';
CREATE DATABASE astrokart OWNER astrokart;
GRANT ALL PRIVILEGES ON DATABASE astrokart TO astrokart;
EOF

sudo systemctl enable postgresql
```

### 0.6 Install Redis

```bash
sudo apt install -y redis-server

# Configure for production
sudo sed -i 's/supervised no/supervised systemd/' /etc/redis/redis.conf

# Optional: set password
# sudo sed -i 's/# requirepass foobared/requirepass your-redis-password/' /etc/redis/redis.conf

sudo systemctl restart redis-server
sudo systemctl enable redis-server

# Verify
redis-cli ping
# PONG
```

### 0.7 Install Docker & Docker Compose

```bash
# Install Docker
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER

# Install Docker Compose plugin
sudo apt install -y docker-compose-plugin

# Verify
docker --version
docker compose version

# Enable and start
sudo systemctl enable docker
sudo systemctl start docker
```

### 0.8 Install Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

### 0.9 Install Supervisor

```bash
sudo apt install -y supervisor
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

### 0.10 Install Certbot (SSL)

```bash
sudo apt install -y certbot python3-certbot-nginx
```

### 0.11 Create Application User

```bash
# Create a dedicated user for the app
sudo useradd -m -s /bin/bash astrokart
sudo usermod -aG www-data astrokart
sudo usermod -aG docker astrokart
```

---

## Step 1: Clone & Install Dependencies

```bash
# Create application directory
sudo mkdir -p /var/www/astrokart
sudo chown astrokart:www-data /var/www/astrokart

# Switch to app user
sudo su - astrokart

# Clone the repository
git clone <repository-url> /var/www/astrokart
cd /var/www/astrokart

# PHP dependencies (production, no dev packages)
composer install --no-dev --optimize-autoloader

# Node dependencies & build frontend assets
npm ci
npm run build

# Set permissions
chmod -R 775 storage bootstrap/cache
chgrp -R www-data storage bootstrap/cache
```

---

## Step 2: Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with production values:

### Core App
```env
APP_NAME="Astrokart"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### Database (MySQL example)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=astrokart
DB_USERNAME=astrokart_user
DB_PASSWORD=<strong-password>
```

### Cache & Queue (Redis recommended)
```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=<redis-password>
```

### SMS OTP (MSG91)
```env
OTP_DRIVER=msg91
MSG91_AUTH_KEY=<your-msg91-auth-key>
MSG91_SENDER_ID=ASTRKRT
MSG91_TEMPLATE_LOGIN=<template-id>
MSG91_TEMPLATE_REGISTRATION=<template-id>
```

### Payment Gateway (SwitchPay)
```env
SWITCHPAY_TOKEN=<your-api-token>
SWITCHPAY_UUID=<your-merchant-uuid>
SWITCHPAY_BASE_URL=https://www.switchpay.in
SWITCHPAY_PAYMENT_MODES=cc|dc|upi|netbanking
```

### Horoscope Microservice
```env
HOROSCOPE_SERVICE_URL=http://localhost:8100
```

### Broadcasting (Reverb — only needed if FEATURE_ASTROLOGERS=true)
```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=astrokart
REVERB_APP_KEY=<secure-random-key>
REVERB_APP_SECRET=<secure-random-secret>
REVERB_HOST=yourdomain.com
REVERB_PORT=443
REVERB_SCHEME=https
REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### Feature Flags
```env
FEATURE_ASTROLOGERS=false
```
Set to `true` when astrologers have been onboarded and the marketplace is ready.

### Mail (for notifications)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourmailprovider.com
MAIL_PORT=587
MAIL_USERNAME=<username>
MAIL_PASSWORD=<password>
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## Step 3: Database Setup

```bash
# Run all migrations
php artisan migrate --force

# Seed required data
php artisan db:seed --class=LanguageSeeder        # 8 languages
php artisan db:seed --class=ExpertiseSeeder        # Astrology specializations
php artisan db:seed --class=PlanEntitlementSeeder  # Subscription entitlements
php artisan db:seed --class=CitySeeder             # 156K cities (requires database/data/cities.csv)

# Create admin user
php artisan tinker --execute "
App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@yourdomain.com',
    'password' => bcrypt('your-secure-password'),
    'role' => 'super_admin',
    'mobile' => '9999999999',
    'mobile_verified_at' => now(),
    'email_verified_at' => now(),
    'preferred_language' => 'en',
    'account_status' => 'active',
]);
"
```

---

## Step 4: Horoscope Microservice (Docker)

```bash
cd /var/www/astrokart

# Build and start the Python horoscope service
docker compose up -d --build horoscope

# Verify it's running (may take ~30s on first start for ephemeris download)
curl http://localhost:8100/api/chart/health
# Expected: {"status":"ok","service":"horoscope"}

# Check logs if issues
docker compose logs horoscope
```

The first request may be slow (~30s) as it downloads the Skyfield ephemeris file (`de421.bsp`). Subsequent requests are fast (<1s).

**Docker Compose service definition** (`docker-compose.yml`):
```yaml
services:
  horoscope:
    build: ./horoscope-service
    ports:
      - "8100:8100"
    restart: unless-stopped
    healthcheck:
      test: ["CMD", "curl", "-f", "http://localhost:8100/api/chart/health"]
      interval: 30s
      timeout: 10s
      retries: 3
      start_period: 30s
```

---

## Step 5: Laravel Optimization

```bash
cd /var/www/astrokart

# Cache configuration, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Create storage symlink (public/storage -> storage/app/public)
php artisan storage:link

# Set ownership and permissions
sudo chown -R astrokart:www-data /var/www/astrokart
sudo chmod -R 775 storage bootstrap/cache

# Ensure PHP-FPM can write to storage
sudo setfacl -R -m u:www-data:rwx storage bootstrap/cache
sudo setfacl -dR -m u:www-data:rwx storage bootstrap/cache
```

---

## Step 6: Queue Worker

The app uses background jobs for consultation billing, daily prediction generation, and subscription renewals.

### Using Supervisor (recommended)

Create `/etc/supervisor/conf.d/astrokart-worker.conf`:
```ini
[program:astrokart-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/astrokart/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=astrokart
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/astrokart/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start astrokart-worker:*
```

---

## Step 7: Scheduled Tasks (Cron)

Add to crontab (`crontab -e`):
```bash
* * * * * cd /var/www/astrokart && php artisan schedule:run >> /dev/null 2>&1
```

**Scheduled tasks** (all in Asia/Kolkata timezone):

| Time | Command | Description |
|---|---|---|
| 00:05 | `subscription:renew-daily` | Auto-renew daily premium passes from wallet |
| 00:10 | `subscription:expire` | Mark expired subscriptions |
| 04:00 | `predictions:generate-daily` | Generate daily predictions for premium users |
| 08:00 | `dasha:send-alerts` | Send Dasha period change notifications |

---

## Step 8: Web Server (Nginx)

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com;

    root /var/www/astrokart/public;
    index index.php;

    ssl_certificate /path/to/ssl/cert.pem;
    ssl_certificate_key /path/to/ssl/key.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Reverb WebSocket proxy (only if FEATURE_ASTROLOGERS=true)
    location /app {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_read_timeout 60s;
        proxy_send_timeout 60s;
    }
}
```

---

## Step 9: Reverb WebSocket Server (Optional)

Only required when `FEATURE_ASTROLOGERS=true` for real-time chat.

### Using Supervisor

Create `/etc/supervisor/conf.d/astrokart-reverb.conf`:
```ini
[program:astrokart-reverb]
command=php /var/www/astrokart/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
user=astrokart
redirect_stderr=true
stdout_logfile=/var/www/astrokart/storage/logs/reverb.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start astrokart-reverb
```

---

## Step 10: SSL/TLS

Use Let's Encrypt with Certbot:
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

---

## Post-Deployment Verification

```bash
# 1. Check app responds
curl -I https://yourdomain.com

# 2. Check horoscope service
curl http://localhost:8100/api/chart/health

# 3. Check queue is processing
php artisan queue:monitor redis:default

# 4. Check scheduled tasks
php artisan schedule:list

# 5. Check logs for errors
tail -f storage/logs/laravel.log

# 6. Run test (optional, needs test database)
php artisan test --compact
```

---

## Deployment Updates

When deploying new code:

```bash
# Switch to app user and project directory
cd /var/www/astrokart

# 1. Pull latest code
git pull origin main

# 2. Install dependencies
composer install --no-dev --optimize-autoloader
npm ci && npm run build

# 3. Run migrations
php artisan migrate --force

# 4. Clear and rebuild caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 5. Restart workers
sudo supervisorctl restart astrokart-worker:*
sudo supervisorctl restart astrokart-reverb  # if running

# 6. Rebuild horoscope service (if Python code changed)
docker compose build horoscope
docker compose up -d horoscope
```

---

## Architecture Diagram

```
                    ┌─────────────┐
                    │   Nginx     │ (HTTPS, port 443)
                    │   Reverse   │
                    │   Proxy     │
                    └──────┬──────┘
                           │
              ┌────────────┼────────────┐
              │            │            │
        ┌─────▼─────┐ ┌───▼───┐ ┌─────▼─────┐
        │  PHP-FPM  │ │Reverb │ │  Static   │
        │  Laravel  │ │  WS   │ │  Assets   │
        │  App      │ │ :8080 │ │ /build/   │
        └─────┬─────┘ └───────┘ └───────────┘
              │
    ┌─────────┼─────────┬──────────┐
    │         │         │          │
┌───▼──┐ ┌───▼───┐ ┌───▼──┐ ┌────▼────┐
│MySQL │ │ Redis │ │Queue │ │Horoscope│
│  DB  │ │Cache/ │ │Worker│ │ Python  │
│      │ │Queue  │ │      │ │ :8100   │
└──────┘ └───────┘ └──────┘ └─────────┘
                                (Docker)
```

---

## Environment-Specific Notes

### Development
```bash
composer run dev  # Starts Laravel server, queue, logs, Vite dev server
```

### NativePHP Mobile (Android)
```bash
php artisan native:install android --force
php artisan native:run android --build=debug
```
Note: Broadcasting is automatically disabled inside NativePHP runtime.

### Feature Flags
| Flag | Default | Description |
|---|---|---|
| `FEATURE_ASTROLOGERS` | `false` | Enables astrologer marketplace, consultations, and real-time chat. Set to `true` when astrologers are onboarded. |

---

## Monitoring & Maintenance

### Log Rotation
Add to `/etc/logrotate.d/astrokart`:
```
/var/www/astrokart/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0664 astrokart www-data
}
```

### Health Checks
- **App**: `GET /` (200 OK)
- **Horoscope**: `GET http://localhost:8100/api/chart/health` (200 OK)
- **Queue**: `php artisan queue:monitor redis:default`
- **Reverb**: Check supervisor status

### Database Backups
```bash
# MySQL
mysqldump -u astrokart_user -p astrokart > backup_$(date +%Y%m%d).sql

# PostgreSQL
pg_dump -U astrokart_user astrokart > backup_$(date +%Y%m%d).sql
```

---

## Troubleshooting

| Issue | Solution |
|---|---|
| 500 error on first load | Check `storage/logs/laravel.log`, ensure `APP_KEY` is set |
| Horoscope service slow on first request | Normal — ephemeris file downloads on cold start (~30s). Subsequent requests are fast. |
| Queue jobs not processing | Check supervisor is running: `supervisorctl status` |
| WebSocket not connecting | Ensure Nginx proxies `/app` to Reverb port, check REVERB_* env vars |
| OTP not sending | Check `OTP_DRIVER=msg91` and MSG91 credentials. Use `OTP_DRIVER=log` for testing. |
| Migrations fail | Check DB credentials and that database exists: `CREATE DATABASE astrokart;` |
| CSS/JS not loading | Run `npm run build` and check `public/build/` exists |
| Permission denied on storage | `chmod -R 775 storage bootstrap/cache` |
