#!/bin/bash

# Cross-browser compatible development server for Gideon's Technology

echo "🚀 Starting Gideon's Technology Development Server"
echo "This server includes CORS headers and disables caching for better Chrome compatibility"

# Try different ports
PORTS=(8080 3000 5000 8888 9000)

for PORT in "${PORTS[@]}"; do
    echo "Attempting to start server on port $PORT..."
    
    # Check if port is available (macOS compatible method)
    if lsof -i :$PORT > /dev/null 2>&1; then
        echo "Port $PORT is already in use. Trying another port..."
        continue
    fi
    
    # Port is available
    echo "✅ Server is starting at http://localhost:$PORT"
    echo "✅ Try opening this URL in Chrome: http://localhost:$PORT"
    echo "Press Ctrl+C to stop the server"
    echo ""
    
    # Start the server with our enhanced dev-server.php script
    # Specify public directory as document root for proper file serving
    php -S localhost:$PORT -t public dev-server.php
    exit 0
done

echo "❌ ERROR: Could not find an available port to start the server."
echo "Please manually check which processes are using your ports with:"
echo "lsof -i -P | grep LISTEN"
exit 1
