# Dynamic Configuration System

This project now uses a dynamic configuration system that automatically detects the environment and generates URLs based on the current server setup.

## 🚀 Quick Start

### 1. Start the PHP Server
```bash
# Use the automated script (recommended)
./start-server.sh

# Or manually
php -S 0.0.0.0:8080
```

### 2. Start the Mobile App
```bash
cd nextupdate-mobile-app
npm start
```

## 🔧 Configuration Features

### Automatic Environment Detection
The system automatically detects the environment based on the host:
- **Development**: `localhost`, `127.0.0.1`, or `192.168.x.x` addresses
- **Staging**: Host contains `staging`
- **Production**: All other hosts

### Dynamic URL Generation
- **App URL**: Automatically generated from current server settings
- **Mobile App URL**: Uses local IP for mobile device access
- **Asset URLs**: All assets use dynamic base URLs

### Environment-Specific Settings
```php
// Development
'app_url' => 'http://192.168.29.174:8080'
'debug' => true

// Production
'app_url' => 'https://yourdomain.com'
'debug' => false
```

## 📱 Mobile App Configuration

### Automatic IP Detection
The mobile app automatically detects the local IP address for WebView access:

```javascript
// Auto-detected configuration
{
  WEBVIEW_URL: 'http://192.168.29.174:8080',
  API_BASE_URL: 'http://192.168.29.174:8080',
  DEBUG: true
}
```

### Environment Variables
You can override the auto-detection using environment variables:
```bash
export EXPO_PUBLIC_LOCAL_IP=192.168.1.100
export EXPO_PUBLIC_PORT=8080
```

## 🛠️ Configuration Files

### PHP Configuration (`config/app.php`)
- Auto-detects environment and app URL
- Provides helper functions for URL generation
- Environment-specific settings

### Mobile App Configuration (`nextupdate-mobile-app/config.js`)
- Auto-detects local IP for development
- Environment-specific URLs
- Fallback configurations

### Helper Scripts
- `start-server.sh`: Starts PHP server with auto-detected IP
- `nextupdate-mobile-app/detect-ip.js`: Detects local IP for mobile app
- `nextupdate-mobile-app/start-dev.sh`: Starts mobile app with auto-configuration

## 🔍 How It Works

### 1. Environment Detection
```php
function getEnvironment() {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    if (strpos($host, 'localhost') !== false || 
        strpos($host, '127.0.0.1') !== false || 
        strpos($host, '192.168.') !== false) {
        return 'development';
    } elseif (strpos($host, 'staging') !== false) {
        return 'staging';
    } else {
        return 'production';
    }
}
```

### 2. URL Generation
```php
function getAppUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
    return $protocol . '://' . $host;
}
```

### 3. Mobile App Integration
```javascript
function getBaseUrl() {
  if (__DEV__) {
    const localIp = process.env.EXPO_PUBLIC_LOCAL_IP || '192.168.29.174';
    const port = process.env.EXPO_PUBLIC_PORT || '8080';
    return `http://${localIp}:${port}`;
  }
  return 'https://nextupdate.com';
}
```

## 📋 Benefits

1. **No Hardcoded URLs**: All URLs are dynamically generated
2. **Environment Awareness**: Automatically adapts to different environments
3. **Mobile-Friendly**: Automatically detects local IP for mobile development
4. **Easy Deployment**: Works across different servers without configuration changes
5. **Development Efficiency**: No need to manually update URLs when changing networks

## 🚨 Important Notes

- The PHP server must be started with `0.0.0.0:8080` to be accessible from mobile devices
- The mobile app will automatically detect the local IP address
- All asset URLs now use the dynamic `base_url()` function
- The system falls back to sensible defaults if auto-detection fails

## 🔧 Troubleshooting

### Mobile App Can't Connect
1. Ensure PHP server is running on `0.0.0.0:8080`
2. Check that the local IP is correctly detected
3. Verify firewall settings allow connections on port 8080

### Wrong URLs Generated
1. Check the `$_SERVER['HTTP_HOST']` value
2. Verify the environment detection logic
3. Manually set environment variables if needed

### Assets Not Loading
1. Ensure all asset URLs use `base_url()` function
2. Check that the app URL is correctly configured
3. Verify file permissions for the public directory

