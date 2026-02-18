#!/bin/bash
# Fix .htaccess permissions on production server
# Run this script on the server via SSH

echo "=== Fixing .htaccess permissions ==="

# Set correct permissions for .htaccess files (644 = readable by Apache)
find /var/www/html -name ".htaccess" -exec chmod 644 {} \;
echo "✓ Set .htaccess files to 644"

# Set correct ownership (www-data is Apache user on Debian)
find /var/www/html -name ".htaccess" -exec chown www-data:www-data {} \;
echo "✓ Set .htaccess ownership to www-data"

# Also fix directory permissions
find /var/www/html -type d -exec chmod 755 {} \;
echo "✓ Set directory permissions to 755"

# Fix file permissions
find /var/www/html -type f -exec chmod 644 {} \;
echo "✓ Set file permissions to 644"

# Make PHP files executable by web server
find /var/www/html -name "*.php" -exec chmod 644 {} \;
echo "✓ Set PHP file permissions to 644"

echo ""
echo "=== Done! Now restart Apache ==="
echo "Run: sudo systemctl restart apache2"
