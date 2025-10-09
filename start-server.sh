#!/bin/bash

# NextUpdate PHP Server Startup Script

echo "🚀 Starting NextUpdate PHP Server"
echo "=================================="

# Detect local IP
echo "🔍 Detecting local IP address..."
LOCAL_IP=$(node -e "
const os = require('os');
const interfaces = os.networkInterfaces();
for (const name of Object.keys(interfaces)) {
    for (const iface of interfaces[name]) {
        if (iface.family === 'IPv4' && !iface.internal) {
            console.log(iface.address);
            process.exit(0);
        }
    }
}
console.log('192.168.29.174'); // fallback
")

PORT="8080"
BASE_URL="http://${LOCAL_IP}:${PORT}"

echo "🌐 Detected local IP: ${LOCAL_IP}"
echo "🔗 Server will be accessible at: ${BASE_URL}"
echo "📱 Mobile app will use: ${BASE_URL}"
echo ""

# Start PHP server
echo "🚀 Starting PHP development server..."
echo "======================================"
echo "Server URL: ${BASE_URL}"
echo "Local URL: http://localhost:${PORT}"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

php -S 0.0.0.0:${PORT}
