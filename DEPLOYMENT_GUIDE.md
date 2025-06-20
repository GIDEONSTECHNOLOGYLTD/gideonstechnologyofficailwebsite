# 🚀 Gideons Technology: Complete Deployment Guide

This document provides detailed step-by-step instructions for deploying the Gideons Technology PHP application to both production (cPanel) and testing (Render) environments.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Local Development Environment](#local-development-environment)
3. [cPanel Deployment (Production)](#cpanel-deployment-production)
4. [Render.com Deployment (Testing)](#rendercom-deployment-testing)
5. [Post-Deployment Configuration](#post-deployment-configuration)
6. [Troubleshooting](#troubleshooting)
7. [Security Considerations](#security-considerations)
8. [Maintenance](#maintenance)

## Prerequisites

Before deploying, ensure you have:

- Git installed on your local machine
- Composer installed on your local machine
- Node.js and NPM installed locally
- Access credentials to your cPanel hosting or Render.com account
- Database credentials for your production/testing environments

## Local Development Environment

### 1. Environment Configuration

```bash
# Clone the repository (if not already done)
git clone https://github.com/yourusername/gideons-technology.git
cd gideons-technology

# Copy and configure the environment file
cp .env.example .env
nano .env
```

Required `.env` settings:
```
APP_NAME="Gideons Technology"
APP_ENV=development
APP_URL=http://localhost:8080
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=gideons_tech
DB_USERNAME=root
DB_PASSWORD=your_password

# Payment gateway credentials
STRIPE_KEY=your_stripe_key
STRIPE_SECRET=your_stripe_secret
PAYPAL_CLIENT_ID=your_paypal_client_id
PAYPAL_SECRET=your_paypal_secret
PAYSTACK_SECRET_KEY=your_paystack_secret_key

# Facebook domain verification
FACEBOOK_DOMAIN_VERIFICATION=your_verification_code
```

### 2. Database Setup

```bash
# Create the database
mysql -u root -p -e "CREATE DATABASE gideons_tech CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations to create tables
php vendor/bin/phinx migrate
```

### 3. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies for frontend assets
npm install

# Compile frontend assets
npm run dev
```

### 4. Start Local Development Server

```bash
# Start PHP development server
php -S localhost:8080 -t public_html/
```

### 5. Verify Local Installation

Navigate to http://localhost:8080 in your browser to ensure the application works properly. Test all key features:

- User registration and login
- Admin dashboard access
- Payment processing
- Referral system functionality

## cPanel Deployment (Production)

### 1. Prepare Your Code for Production

```bash
# Switch to production branch (if applicable)
git checkout main

# Update dependencies to latest versions
composer update
npm update

# Set proper environment for production
cp .env.example .env.production
nano .env.production
```

Update the `.env.production` file with production values:
```
APP_NAME="Gideons Technology"
APP_ENV=production
APP_URL=https://gideonstechnology.com
APP_DEBUG=false
...
```

### 2. Optimize for Production

```bash
# Build assets for production
npm run build

# Optimize composer autoloader
composer install --optimize-autoloader --no-dev
```

### 3. Create Deployment Package

```bash
# Archive the project for deployment
git archive --format=zip --output=gideonstech_deploy.zip HEAD
```

### 4. Upload and Extract on cPanel

1. Log in to your cPanel account on HarmonWeb
2. Navigate to **File Manager**
3. Navigate to the directory for your domain (typically `public_html`)
4. Click "Upload" and select your `gideonstech_deploy.zip` file
5. Once uploaded, select the file and click "Extract"
6. Extract the contents to your desired location (e.g., `public_html`)

### 5. Configure cPanel Environment

1. Upload your `.env.production` file (rename to `.env`)
2. Set proper permissions:
   ```bash
   chmod -R 755 public_html
   chmod -R 755 storage
   chmod 644 .htaccess
   
   # Set specific permissions for storage directories
   find storage -type d -exec chmod 755 {} \;
   find storage -type f -exec chmod 644 {} \;
   ```

### 6. Database Configuration on cPanel

1. In cPanel, navigate to **MySQL® Databases**
2. Create a new database (e.g., `gideonstech_db`)
3. Create a database user with a strong password
4. Add the user to the database with "All Privileges" selected
5. Update your `.env` file with these database credentials

### 7. Run Database Migrations

If you have SSH access to your cPanel hosting:

```bash
cd public_html
php vendor/bin/phinx migrate
```

If you don't have SSH access:
1. Export your database schema locally:
   ```bash
   php vendor/bin/phinx migrate --dry-run > schema.sql
   ```
2. In cPanel, go to **phpMyAdmin**
3. Select your database
4. Click "Import" and upload the `schema.sql` file

### 8. Configure Domain and SSL

1. Ensure your domain points to the correct directory
2. Install an SSL certificate:
   - In cPanel, go to **SSL/TLS** > **Install SSL Certificate**
   - Follow the wizard to install a free Let's Encrypt certificate
3. Force HTTPS by adding to `.htaccess`:
   ```
   RewriteEngine On
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

### 9. Verify cPanel Deployment

Visit your domain (https://gideonstechnology.com) and verify:
- Website loads correctly
- All pages function without errors
- User registration and login work
- Admin dashboard is accessible
- Payment gateways are connected
- Referral system works as expected

## Render.com Deployment (Testing)

### 1. Prepare for Render Deployment

```bash
# Create a Procfile for Render (if not already created)
echo "web: php -S 0.0.0.0:\$PORT public_html/index.php" > Procfile

# Create a specific .env file for Render
cp .env.example .env.render
nano .env.render
```

Customize `.env.render` for the Render environment:
```
APP_NAME="Gideons Technology (Testing)"
APP_ENV=testing
APP_URL=https://gideonstech.onrender.com
APP_DEBUG=true
DB_CONNECTION=pgsql
...
```

### 2. Create a Render Account & Project

1. Sign up at [render.com](https://render.com/) if you haven't already
2. Connect your GitHub repository to Render
   - Click "New" > "Web Service"
   - Select your GitHub repository
   - Choose the branch to deploy (usually `main`)

### 3. Configure Render Web Service

Configure with these settings:
1. **Name**: `gideonstechnology` (or your preferred name)
2. **Environment**: `PHP`
3. **Region**: Choose the one closest to your target audience
4. **Build Command**: `composer install --no-dev && npm install && npm run build`
5. **Start Command**: `php -S 0.0.0.0:$PORT public_html/index.php`
6. **Environment Variables**:
   - Add all variables from your `.env.render` file
   - Make sure to set `PORT=10000` (or any port Render supports)

### 4. Database Setup on Render

1. From the Render dashboard, click "New" > "PostgreSQL"
2. Configure your database:
   - **Name**: `gideonstech-db` (or your preferred name)  
   - **Region**: Same as your web service
   - **PostgreSQL Version**: 14 or newer
3. Once created, update your web service environment variables with the database connection details provided by Render

### 5. Deploy and Run Migrations

1. Click "Create Web Service" to deploy your application
2. Once deployed, use Render Shell to run migrations:
   - Go to your Web Service > Shell
   - Run: `php vendor/bin/phinx migrate`

### 6. Verify Render Deployment

Visit your Render URL (provided in the dashboard) and verify:
- Website loads correctly in testing environment
- All features work as expected
- Any test accounts can be created and used

## Post-Deployment Configuration

### 1. Payment Gateway Configuration

#### Stripe Setup
1. Log in to your [Stripe Dashboard](https://dashboard.stripe.com/)
2. Navigate to **Developers** > **Webhooks**
3. Add endpoint: `https://yourdomain.com/webhooks/stripe`
4. Select events to listen for (e.g., `payment_intent.succeeded`, `payment_intent.failed`)

#### PayPal Setup
1. Log in to your [PayPal Developer Dashboard](https://developer.paypal.com/dashboard/)
2. Navigate to your app settings
3. Update the return and cancel URLs to match your production domain
4. Set IPN notification URL: `https://yourdomain.com/webhooks/paypal`

#### Paystack Setup
1. Log in to your [Paystack Dashboard](https://dashboard.paystack.com/)
2. Navigate to **Settings** > **API Keys & Webhooks**
3. Update callback URL: `https://yourdomain.com/webhooks/paystack`

### 2. Email Configuration

For transactional emails (welcome emails, password resets, referral notifications):

1. Update your `.env` file with SMTP settings:
```
MAIL_DRIVER=smtp
MAIL_HOST=smtp.yourdomain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Gideons Technology"
```

2. For cPanel:
   - Configure an email account in cPanel
   - Use those credentials in your `.env` file

3. For Render (testing):
   - Consider using a service like Mailtrap or Mailgun
   - Configure the service and update your `.env.render` file

### 3. Social Media Sharing Setup

For the referral system social sharing:

1. Facebook:
   - Create a Facebook App at [developers.facebook.com](https://developers.facebook.com/)
   - Add your domain to the app settings
   - Update your `.env` file with the App ID:
     ```
     FACEBOOK_APP_ID=your_app_id
     ```

2. Twitter:
   - No additional setup required, but ensure your meta tags are configured correctly

### 4. Analytics Integration

1. Google Analytics:
   - Create a property in Google Analytics
   - Add your tracking code to the template files 
   - Update the template file: `templates/layouts/footer.php`

## Troubleshooting

### Database Connection Issues

**Symptoms:**
- "Could not connect to database" errors
- White screen when accessing database-dependent pages

**Solutions:**
1. Verify database credentials in `.env` file
2. Check if database server is running
3. Test connection manually:
   ```bash
   php -r "try { new PDO('mysql:host=localhost;dbname=your_db', 'user', 'pass'); echo 'Connected successfully'; } catch(PDOException \$e) { echo \$e->getMessage(); }"
   ```
4. Check database user permissions
5. Verify IP restrictions (especially on remote databases)

### File Permission Issues

**Symptoms:**
- "Permission denied" errors in logs
- Can't write to log or cache files
- Upload functionality not working

**Solutions:**
1. Set proper directory permissions:
   ```bash
   chmod -R 755 .
   find storage -type d -exec chmod 755 {} \;
   find storage -type f -exec chmod 644 {} \;
   chmod -R 775 storage/logs
   chmod -R 775 storage/cache
   ```
2. Check ownership:
   ```bash
   chown -R www-data:www-data .  # For Apache
   ```

### Payment Gateway Failures

**Symptoms:**
- Payments not processing
- Gateway errors in checkout

**Solutions:**
1. Double-check API keys in `.env`
2. Verify webhook URLs are correctly configured
3. Check payment gateway dashboards for error logs
4. Test with sandbox/test mode first
5. Check for SSL/HTTPS issues (most gateways require HTTPS)

### Referral System Issues

**Symptoms:**
- Referral links not tracking properly
- Rewards not being properly recorded

**Solutions:**
1. Check session handling (referral codes are stored in session)
2. Verify database tables are created correctly
3. Run test transactions to trace the referral workflow
4. Check for JavaScript errors affecting the copy/share buttons

## Security Considerations

### Environment Protection

Prevent access to your `.env` file:
```
# Add to .htaccess
<Files .env>
    Order allow,deny
    Deny from all
</Files>
```

### Database Backups

Regular backups are essential:

```bash
# Create a backup script (backup.sh)
#!/bin/bash
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="/path/to/backup/directory"

# For MySQL
mysqldump -u username -p password databasename > $BACKUP_DIR/database_$TIMESTAMP.sql

# Compress the backup
gzip $BACKUP_DIR/database_$TIMESTAMP.sql

# Delete backups older than 30 days
find $BACKUP_DIR -name "database_*.sql.gz" -mtime +30 -delete
```

Make it executable and add to cron:
```bash
chmod +x backup.sh
crontab -e
# Add: 0 2 * * * /path/to/backup.sh
```

### SSL Configuration

Optimize your SSL configuration in `.htaccess`:
```
# Force HTTPS
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# HSTS (uncomment after confirming HTTPS works properly)
# Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains" env=HTTPS

# Prevent iframe embedding
Header always set X-Frame-Options "SAMEORIGIN"

# XSS protection
Header always set X-XSS-Protection "1; mode=block"
Header always set X-Content-Type-Options "nosniff"
```

## Maintenance

### Regular Updates

Create a maintenance schedule:

1. **Weekly Tasks**:
   - Check error logs
   - Monitor disk space usage
   - Review failed login attempts

2. **Monthly Tasks**:
   - Update PHP dependencies: `composer update`
   - Update Node.js dependencies: `npm update`
   - Test core functionality after updates
   - Backup database

3. **Quarterly Tasks**:
   - Review and update SSL certificates
   - Security audit of code and dependencies
   - Performance optimization review
   - User interface improvements

### Logging and Monitoring

Configure comprehensive logging:

1. Add to `.env`:
   ```
   LOG_CHANNEL=daily
   LOG_LEVEL=warning
   ```

2. Monitor logs regularly:
   ```bash
   # Check for errors
   grep -i error storage/logs/app.log
   
   # Monitor in real-time
   tail -f storage/logs/app.log
   ```

3. Consider setting up log rotation:
   ```bash
   # Add to crontab
   0 0 * * * cd /path/to/app && php utilities/rotate_logs.php
   ```

---

## Conclusion

This deployment guide covers the essential steps for deploying and maintaining your Gideons Technology application. By following these instructions carefully, you should be able to successfully deploy to both production (cPanel) and testing (Render) environments.

If you encounter any issues not covered in this guide, refer to the official documentation for the specific services (cPanel, Render, Stripe, etc.) or consult with your development team.

---

**Last Updated**: June 2025
**Version**: 1.0
