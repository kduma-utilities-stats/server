#!/bin/bash

# Get directory of this script
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Delete build directory if it exists
if [ -d "$DIR/../build" ]; then
    rm -rf "$DIR/../build"
fi

# Create build/src build/php build/nginx directory
mkdir -p "$DIR/../build/src"

# Get current git branch
BRANCH=$(git rev-parse --abbrev-ref HEAD)

# Copy clean files to build directory
cd "$DIR/../" && git archive --format tar --output "$DIR/../build/src.tar" "$BRANCH"

# Extract files to build directory
tar -xf "$DIR/../build/src.tar" -C "$DIR/../build/src"

# Delete tar file
rm "$DIR/../build/src.tar"

# Build docker image with target composer_base
COMPOSER_BASE_IMAGE=$(docker build --target composer_base --file "$DIR/../Dockerfile" "$DIR/../build/src" --quiet)

# Build docker image with target vite_base
#VITE_BASE_IMAGE=$(docker build --target vite_base --file "$DIR/../Dockerfile" "$DIR/../build/src" --quiet)

# Run composer install on composer_base image
docker run --rm --volume "$DIR/../build/src:/var/www/html" "$COMPOSER_BASE_IMAGE" composer install --ignore-platform-reqs --prefer-dist --no-scripts --no-progress --no-interaction --no-dev --no-autoloader

# Run composer dump-autoload on composer_base image
docker run --rm --volume "$DIR/../build/src:/var/www/html" "$COMPOSER_BASE_IMAGE" composer dump-autoload --optimize --apcu --no-dev

# Run view:cache artisan command on composer_base image
docker run --rm --volume "$DIR/../build/src:/var/www/html" "$COMPOSER_BASE_IMAGE" php artisan view:cache

# Run event:cache artisan command on composer_base image
docker run --rm --volume "$DIR/../build/src:/var/www/html" "$COMPOSER_BASE_IMAGE" php artisan event:cache

# Run settings:discover artisan command on composer_base image
#docker run --rm --volume "$DIR/../build/src:/var/www/html" "$COMPOSER_BASE_IMAGE" php artisan settings:discover

# Run livewire:discover artisan command on composer_base image
#docker run --rm --volume "$DIR/../build/src:/var/www/html" "$COMPOSER_BASE_IMAGE" php artisan livewire:discover

# Run npm ci on vite_base image
#docker run --rm --volume "$DIR/../build/src:/var/www/html" "$VITE_BASE_IMAGE" npm ci

# Run npm run build on vite_base image
#docker run --rm --volume "$DIR/../build/src:/var/www/html" "$VITE_BASE_IMAGE" npm run build

# remove node_modules
#rm -rf "$DIR/../build/src/node_modules"

#remove all .gitignore files from build directory
find "$DIR/../build/src" -name ".gitignore" -type f -delete

# Copy required files to php folder
mkdir -p "$DIR/../build/php" && cp -r \
    "$DIR/../build/src/artisan" \
    "$DIR/../build/src/composer.json" \
    "$DIR/../build/src/app" \
    "$DIR/../build/src/bootstrap" \
    "$DIR/../build/src/config" \
    "$DIR/../build/src/routes" \
    "$DIR/../build/src/storage" \
    "$DIR/../build/src/vendor" \
    "$DIR/../build/php"
#   "$DIR/../build/src/lang" \

mkdir -p "$DIR/../build/php/resources" && cp -r \
    "$DIR/../build/src/resources/views" \
    "$DIR/../build/php/resources"
#   "$DIR/../build/src/resources/markdown" \

mkdir -p "$DIR/../build/php/public" && cp -r \
    "$DIR/../build/src/public/index.php" \
    "$DIR/../build/php/public"

mkdir -p "$DIR/../build/php/database" && cp -r \
    "$DIR/../build/src/database/migrations" \
    "$DIR/../build/php/database"
#   "$DIR/../build/src/database/settings" \

#mkdir -p "$DIR/../build/php/public/build" && cp -r \
#    "$DIR/../build/src/public/build/manifest.json" \
#    "$DIR/../build/php/public/build"

# Copy required files to nginx folder
mkdir -p "$DIR/../build/nginx" && cp -r \
    "$DIR/../build/src/public" \
    "$DIR/../build/nginx"
