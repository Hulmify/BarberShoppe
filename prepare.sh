# The script is used to prepare the project for deployment on Cpanel

# Delete previous zip
rm app.zip

# Build assets
npm install
npm run build

# Prepare
composer install --optimize-autoloader
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Create HTaccess file for Cpanel deployment
FILE_NAME=".htaccess"

cat <<EOL > "$FILE_NAME"
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^(.*)\$ public/\$1 [L]
</IfModule>
EOL

echo "File '$FILE_NAME' created with content."


# Zip project excluding dev files
zip -r app.zip . \
    -x ".git/*" \
    -x ".env" \
    -x "tests/*" \
    -x "node_modules/*" \
    -x ".DS_Store" \
    -x ".gitignore" \
    -x ".gitattributes" \
    -x ".env.example" \
    -x ".editorconfig" \
    -x "prepare.sh"

# Clean up
rm -f "$FILE_NAME"

# Command to run migrations
echo "Run these commands on the server after uploading the zip file and extracting it"
echo "/opt/cloudlinux/alt-php84/root/usr/bin/php artisan migrate --force"