# 🛠️ ClientBridge Server Setup Guide

> A concise, battle-tested summary of everything needed to spin up and manage a production-ready Laravel app on a \$5 VPS with Git-powered deploys.

---

## 📦 Base System Setup

```bash
# Update system & install essentials
sudo apt update && sudo apt upgrade -y
sudo apt install -y nginx php php-fpm php-mysql php-cli php-curl php-mbstring php-xml php-bcmath php-zip unzip git curl sqlite3 php-sqlite3 npm
```

## 🔐 Secure the Box

```bash
# Set hostname
sudo hostnamectl set-hostname houston1.oldlinecyber.com

# Configure UFW
sudo ufw allow OpenSSH
sudo ufw allow 80,443/tcp
sudo ufw enable

# Swapfile setup (optional but recommended on 1GB VPS)
sudo fallocate -l 1G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

## 🔧 Nginx Config

```bash
# Create new site config
sudo nano /etc/nginx/sites-available/clientbridge

# Example config:
server {
    listen 80;
    server_name houston1.oldlinecyber.com;

    root /home/alex/clientbridge/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}

# Enable and reload
sudo ln -s /etc/nginx/sites-available/clientbridge /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
```

## 🔐 SSL via Certbot

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d houston1.oldlinecyber.com
```

## 🐘 Laravel Deployment

```bash
cd ~
composer create-project laravel/laravel clientbridge
cd clientbridge
cp .env.example .env
php artisan key:generate
```

### Permissions (VERY IMPORTANT)

```bash
sudo chown -R www-data:www-data storage bootstrap/cache
touch storage/logs/laravel.log
sudo chmod -R 775 storage bootstrap/cache
```

## 🗄️ Database: SQLite

```bash
touch database/database.sqlite
sudo chown www-data:www-data database/database.sqlite
sudo chmod 664 database/database.sqlite

# In .env:
DB_CONNECTION=sqlite
DB_DATABASE=/home/alex/clientbridge/database/database.sqlite
```

## 🔁 Git + Deploy

```bash
# Set up SSH key
ssh-keygen -t ed25519 -C "houston1.oldlinecyber.com"
cat ~/.ssh/id_ed25519.pub # add to GitHub deploy keys

# Repo setup
cd ~/clientbridge
git init
git remote add origin git@github.com:alexramsey92/clientbridge-laravel.git
git pull origin main
```

### Example deploy script

```bash
nano ~/deploy-clientbridge.sh
```

```bash
#!/bin/bash
cd ~/clientbridge || exit
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo systemctl restart php8.3-fpm
```

```bash
chmod +x ~/deploy-clientbridge.sh
```

## 🔍 Useful Commands

```bash
# Laravel
php artisan route:list
php artisan migrate --force
php artisan config:clear && php artisan config:cache

# Logs
sudo tail -f /var/log/nginx/error.log
tail -f storage/logs/laravel.log

# Nginx & PHP
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx

# Check server status
uptime
df -h
free -h
sudo journalctl -xe
```

---

## 💡 Notes

* Stick to `chmod 775` for directories Laravel needs to write to.
* Use `www-data` for Nginx/PHP file access.
* Swap + SQLite is a winning combo for tiny VPS deployments.

---

Happy shipping! 🚀

---

*This file lives in your repo as `SERVER_SETUP.md` — keep it updated!*
