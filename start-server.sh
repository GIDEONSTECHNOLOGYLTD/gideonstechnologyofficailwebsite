#!/bin/bash

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "PHP is not installed. Please install PHP to run this server."
    exit 1
fi

# Get the directory of the script
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Navigate to the project directory
cd "$DIR"

# This script helps start the PHP server on an available port
# starting from 8080 and incrementing until it finds an open port

PORT=8080
MAX_PORT=8090

while [ $PORT -le $MAX_PORT ]; do
  if lsof -Pi :$PORT -sTCP:LISTEN -t >/dev/null ; then
    echo "Port $PORT is in use, trying next port..."
    PORT=$((PORT+1))
  else
    echo "Starting PHP server on port $PORT..."
    php -S localhost:$PORT router.php
    exit 0
  fi
done

echo "Could not find an available port between 8080-$MAX_PORT. Please free up a port and try again."
exit 1
