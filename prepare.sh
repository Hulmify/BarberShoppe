# The script is used to prepare the project for deployment on Cpanel

# Delete zips directory
rm -rf zips

# Create zips directory
mkdir zips

# Build assets
npm install
npm run build

# Prepare
composer install --optimize-autoloader
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear


# Zip project excluding dev files
zip -r zips/app.zip . \
    -x ".git/*" \
    -x ".env" \
    -x "tests/*" \
    -x "node_modules/*" \
    -x ".DS_Store" \
    -x ".gitignore" \
    -x ".gitattributes" \
    -x ".env.example" \
    -x ".editorconfig" \
    -x "prepare.sh" \
    -x "vendor/*"

# Zip vendor
zip -r zips/vendor.zip vendor

# Command to run migrations
echo "Run these commands on the server after uploading the zip file and extracting it"
echo "/opt/cloudlinux/alt-php84/root/usr/bin/php artisan migrate --force"