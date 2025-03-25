# Simplified Deployment Guide for Ubuntu

This guide provides instructions for deploying the Maintenance App on a new Ubuntu server using an automated script.

## Prerequisites

- A fresh Ubuntu server (Ubuntu 20.04 LTS or Ubuntu 22.04 LTS recommended)
- Root or sudo access to the server
- A domain name pointed to your server's IP address (optional, but recommended)

## Quick Deployment

1. Copy both the `deploy.sh` script and your application code to your Ubuntu server.

2. Make the deployment script executable:
   ```bash
   chmod +x deploy.sh
   ```

3. Run the script with sudo:
   ```bash
   sudo ./deploy.sh
   ```

4. Follow the interactive prompts to configure your installation:
   - Domain name
   - Database credentials
   - PayPal integration details (optional)
   - SSL configuration (optional)
   - Repository URL
   - Installation directory

5. The script will automatically:
   - Install all required system packages
   - Set up MySQL with your specified database and user
   - Clone your repository
   - Install PHP and JavaScript dependencies
   - Configure the application
   - Set up Nginx web server
   - Configure Laravel queue worker
   - Set up the task scheduler
   - Set up SSL with Let's Encrypt (if selected)
   - Set proper file permissions

6. When the installation completes, you'll see a summary with:
   - The URL to access your application
   - Default login credentials
   - Database information

## Manual Configuration (If Needed)

If you need to make additional customizations after running the script, refer to the `deploy_instructions.md` file for detailed steps.

## Default Login Credentials

- **Super Property Manager**:
  - Email: admin@example.com
  - Password: password

- **Property Manager**:
  - Email: manager@example.com
  - Password: password

- **Technicians**:
  - Email: john@example.com
  - Password: password
  
  - Email: jane@example.com
  - Password: password

**IMPORTANT**: Change these default passwords immediately after first login!

## Troubleshooting

If you encounter any issues during the deployment:

1. Check the error message displayed by the script
2. Review the relevant log files:
   - Nginx logs: `/var/log/nginx/error.log`
   - PHP-FPM logs: `/var/log/php8.2-fpm.log`
   - Laravel logs: `<installation_dir>/storage/logs/laravel.log`

## Security Considerations

After deployment, consider implementing these additional security measures:

1. Update all default passwords
2. Configure a firewall (UFW)
3. Set up fail2ban to protect against brute force attacks
4. Implement regular backups
5. Configure MySQL security best practices 