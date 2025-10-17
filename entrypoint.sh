#!/bin/sh
set -e

if [ ! -d "vendor" ]; then
  echo "ENTRYPOINT: Installing dependencies"
  composer install --no-interaction --prefer-dist
fi

echo "ENTRYPOINT: App is ready"

# Keep container alive
exec sleep infinity