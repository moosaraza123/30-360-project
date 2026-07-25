# Deployment Runbook — 30360calculator.com

*Target: DigitalOcean droplet (1 GB / Ubuntu 24.04), nginx + PHP 8.3-FPM + MySQL 8, free SSL.*

## 0. Prerequisites (user provides)
- [ ] Domain purchased: **30360calculator.com** (Namecheap, no upsells)
- [ ] Droplet created: Basic Regular $6/mo, FRA1, Ubuntu 24.04, SSH key auth, weekly backups ON
- [ ] Droplet IP: `___________`
- [ ] SSH access from dev machine (`ssh root@IP` works)
- [ ] Resend account (free) + API key, with domain verified (SPF/DKIM records) — can follow after launch; use log driver until then

## 1. DNS (Namecheap → Advanced DNS, or move NS to free Cloudflare)
| Type | Host | Value |
|---|---|---|
| A | @ | droplet IP |
| A | www | droplet IP |
(Cloudflare optional but recommended later: free CDN + caching. Set SSL mode "Full (strict)" AFTER certbot runs.)

## 2. Server provisioning (run as root on droplet)
```bash
adduser deploy --disabled-password && usermod -aG sudo deploy
rsync -a ~/.ssh /home/deploy/ && chown -R deploy:deploy /home/deploy/.ssh

apt update && apt upgrade -y
apt install -y nginx mysql-server php8.3-fpm php8.3-mysql php8.3-xml php8.3-mbstring \
  php8.3-curl php8.3-zip php8.3-gd php8.3-bcmath php8.3-intl unzip git acl
curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
curl -fsSL https://deb.nodesource.com/setup_22.x | bash - && apt install -y nodejs

# firewall
ufw allow OpenSSH && ufw allow 'Nginx Full' && ufw --force enable

# mysql
mysql_secure_installation   # set root pw, remove anon users/test db
mysql -e "CREATE DATABASE calc30360 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'calc'@'localhost' IDENTIFIED BY '<STRONG_PW>';
GRANT ALL ON calc30360.* TO 'calc'@'localhost'; FLUSH PRIVILEGES;"
```

## 3. App deploy
```bash
sudo -u deploy git clone <repo-url> /var/www/30360calculator
cd /var/www/30360calculator
cp .env.example .env    # then edit per §4
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache && php artisan route:cache && php artisan view:cache
chown -R deploy:www-data storage bootstrap/cache && chmod -R ug+rwx storage bootstrap/cache
# super admin (values in .env): php artisan db:seed --class=SuperAdminSeeder --force
```

## 4. Production .env (critical values)
```
APP_NAME="30/360 Calculator"
APP_ENV=production
APP_DEBUG=false                        # NON-NEGOTIABLE
APP_URL=https://30360calculator.com
DB_DATABASE=calc30360  DB_USERNAME=calc  DB_PASSWORD=<STRONG_PW>
SESSION_SECURE_COOKIE=true
MAIL_MAILER=resend (or log until Resend is set up)
MAIL_FROM_ADDRESS="no-reply@30360calculator.com"
SUPER_ADMIN_EMAIL=... SUPER_ADMIN_PASSWORD=<fresh, never the old one>
ADSENSE_CLIENT=            # empty until AdSense approval
# GOOGLE_ANALYTICS_ID=     # if using GA (config/services.php)
```

## 5. nginx + SSL
```bash
# /etc/nginx/sites-available/30360calculator — standard Laravel server block:
#   root /var/www/30360calculator/public; index index.php;
#   server_name 30360calculator.com www.30360calculator.com;
#   location / { try_files $uri $uri/ /index.php?$query_string; }
#   location ~ \.php$ { include snippets/fastcgi-php.conf; fastcgi_pass unix:/run/php/php8.3-fpm.sock; }
ln -s /etc/nginx/sites-available/30360calculator /etc/nginx/sites-enabled/
nginx -t && systemctl reload nginx

apt install -y certbot python3-certbot-nginx
certbot --nginx -d 30360calculator.com -d www.30360calculator.com   # auto-renews
```

## 6. Queue worker + scheduler
```bash
# /etc/systemd/system/calc-queue.service → ExecStart=/usr/bin/php /var/www/30360calculator/artisan queue:work --tries=3
systemctl enable --now calc-queue
# cron (deploy user): * * * * * cd /var/www/30360calculator && php artisan schedule:run >> /dev/null 2>&1
```

## 7. Post-launch checklist
- [ ] https://30360calculator.com/calculator loads, SSL padlock, no mixed content
- [ ] Calculation, comparison, export, register→verification email all work
- [ ] Google Search Console: verify property, submit https://30360calculator.com/sitemap.xml
- [ ] Bing Webmaster Tools (free traffic, 2 minutes)
- [ ] Uptime monitor (free: UptimeRobot) on /calculator
- [ ] `php artisan about` shows production, debug off, caches cached

## 8. Redeploy procedure (after future changes)
```bash
cd /var/www/30360calculator && git pull
composer install --no-dev --optimize-autoloader && npm ci && npm run build
php artisan migrate --force && php artisan config:cache route:cache view:cache
systemctl restart calc-queue
```

## Pre-launch code items still pending (do before or right after launch)
- Privacy policy + terms pages (required for AdSense)
- Scheduled cleanup of guest `calculations.ip_address` (retention)
- Queue the verification/subscription emails (`ShouldQueue`)
