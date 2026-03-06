# MySQL Local Development Setup

This guide covers setting up MySQL locally for ClientBridge Laravel development.

## Prerequisites

- Laravel Herd (includes MySQL)
- Or manual MySQL installation
- PHP with `pdo_mysql` extension

## Database Configuration

Your `.env` file should be configured with:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USERNAME=root
DB_PASSWORD=localdb
DB_DATABASE=clientbridge_local
```

## Setup Steps

### 1. Start MySQL Service

**With Laravel Herd:**
```bash
# Herd automatically manages MySQL
herd start
```

**Manual MySQL:**
```bash
# Windows (if installed manually)
net start mysql

# Or via Services.msc - start "MySQL" service
```

### 2. Create Database

**Option A: Using MySQL Command Line**
```bash
# Connect to MySQL
mysql -u root -p

# Create database
CREATE DATABASE clientbridge_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Create user (optional - for production-like setup)
CREATE USER 'clientbridge'@'localhost' IDENTIFIED BY 'localdb';
GRANT ALL PRIVILEGES ON clientbridge_local.* TO 'clientbridge'@'localhost';
FLUSH PRIVILEGES;

# Exit
EXIT;
```

**Option B: Using Laravel Artisan**
```bash
# Laravel can create the database if it doesn't exist
php artisan db:create
```

### 3. Update Environment File

Update your `.env` file with the database name:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clientbridge_local
DB_USERNAME=root
DB_PASSWORD=localdb
```

### 4. Test Database Connection

```bash
# Test the connection
php artisan db:show

# Or check migration status
php artisan migrate:status
```

## Database Setup Commands

### Fresh Installation
```bash
# Run migrations and seed database
php artisan migrate:fresh --seed
```

### Individual Commands
```bash
# Run migrations only
php artisan migrate

# Seed database only
php artisan db:seed

# Specific seeder
php artisan db:seed --class=UserSeeder
```

## Available Seeders

The following seeders are available in this application:

- **UserSeeder** - Creates admin and test users
- **ClientSeeder** - Creates sample client records
- **LeadFormSeeder** - Creates lead form templates
- **MessageSeeder** - Creates sample messages

### Running All Seeders
```bash
php artisan db:seed
```

### Running Individual Seeders
```bash
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=ClientSeeder
php artisan db:seed --class=LeadFormSeeder
php artisan db:seed --class=MessageSeeder
```

## Database Management

### Reset Database
```bash
# Drop all tables and re-run migrations with seeders
php artisan migrate:fresh --seed
```

### Rollback Migrations
```bash
# Rollback last batch
php artisan migrate:rollback

# Rollback specific number of batches
php artisan migrate:rollback --step=3

# Rollback all migrations
php artisan migrate:reset
```

### Check Migration Status
```bash
php artisan migrate:status
```

## Troubleshooting

### Connection Issues

**Error: "Access denied for user 'root'@'localhost'"**
```bash
# Reset MySQL root password (with Herd)
herd mysql:reset-password

# Or manually connect and change password
mysql -u root
ALTER USER 'root'@'localhost' IDENTIFIED BY 'localdb';
FLUSH PRIVILEGES;
```

**Error: "Database does not exist"**
```bash
# Create the database manually
mysql -u root -p
CREATE DATABASE clientbridge_local;
```

### Permission Issues
```bash
# Grant all privileges to user
mysql -u root -p
GRANT ALL PRIVILEGES ON clientbridge_local.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
```

### Check MySQL Service Status

**With Herd:**
```bash
herd status
```

**Manual Installation:**
```bash
# Windows
sc query mysql

# Check if MySQL is running
tasklist | findstr mysql
```

## Switching Between SQLite and MySQL

### To MySQL
1. Update `.env` file:
   ```env
   DB_CONNECTION=mysql
   ```
2. Create MySQL database
3. Run migrations:
   ```bash
   php artisan migrate:fresh --seed
   ```

### To SQLite
1. Update `.env` file:
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=./database/database.sqlite
   ```
2. Create SQLite file:
   ```bash
   touch database/database.sqlite
   ```
3. Run migrations:
   ```bash
   php artisan migrate:fresh --seed
   ```

## Useful MySQL Commands

### Database Information
```sql
-- Show all databases
SHOW DATABASES;

-- Show all tables in current database
SHOW TABLES;

-- Show table structure
DESCRIBE users;

-- Show table indexes
SHOW INDEX FROM users;
```

### Data Queries
```sql
-- Check user count
SELECT COUNT(*) FROM users;

-- Show admin users
SELECT id, name, email, role FROM users WHERE role = 'company_administrator';

-- Show recent migrations
SELECT * FROM migrations ORDER BY batch DESC LIMIT 5;
```

## Performance Tips

### Optimize MySQL for Development
```sql
-- Add to MySQL config (my.cnf or my.ini)
[mysqld]
innodb_buffer_pool_size = 256M
max_connections = 100
query_cache_size = 64M
```

### Laravel Optimizations
```bash
# Clear query cache
php artisan cache:clear

# Optimize config for production
php artisan config:cache
```

## Backup and Restore

### Backup Database
```bash
# Create backup
mysqldump -u root -p clientbridge_local > backup.sql

# Or with Laravel
php artisan db:backup
```

### Restore Database
```bash
# Restore from backup
mysql -u root -p clientbridge_local < backup.sql
```

---

## Quick Reference

**Essential Commands:**
```bash
# Setup new database
mysql -u root -p
CREATE DATABASE clientbridge_local;

# Update .env and run
php artisan migrate:fresh --seed

# Test connection
php artisan db:show
```

**Default Credentials:**
- Host: `127.0.0.1`
- Port: `3306`  
- Username: `root`
- Password: `localdb`
- Database: `clientbridge_local`