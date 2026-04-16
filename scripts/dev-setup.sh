#!/bin/bash

# Start original dockware entrypoint in background
/entrypoint.sh &

echo "Waiting for Shopware to be ready..."
until curl -s http://localhost/health > /dev/null; do
    sleep 5
done

echo "Installing and activating FibProductLabel..."
php bin/console plugin:refresh
php bin/console plugin:install --activate FibProductLabel

echo "Building Administration..."
./bin/build-administration.sh

echo "Setup complete. Keeping container alive."
# Wait for the background process (the main entrypoint) to keep the container running
wait
