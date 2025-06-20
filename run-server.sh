#!/bin/bash

echo "Starting Gideon's Technology website..."

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "PHP is not installed. Please install PHP to run this server."
    exit 1
fi

# Get the directory of the script
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

# Navigate to the project directory
cd "$DIR"

# Clear any previous errors
if [ -f "$DIR/error.log" ]; then
    rm "$DIR/error.log"
fi

echo "Opening browser to http://localhost:3000 (this will happen in 3 seconds)"

# Wait 3 seconds before opening browser
sleep 3

# Open browser on various operating systems
if [ "$(uname)" == "Darwin" ]; then
    # Mac OS X
    open "http://localhost:3000"
elif [ "$(uname)" == "Linux" ]; then
    # Linux
    if command -v xdg-open &> /dev/null; then
        xdg-open "http://localhost:3000"
    else
        echo "Cannot open browser automatically. Please navigate to http://localhost:3000"
    fi
else
    # Windows
    start "http://localhost:3000"
fi

# Start the PHP server on port 3000
echo "Server started! Press Ctrl+C to stop."
php -S localhost:3000 2> "$DIR/error.log"