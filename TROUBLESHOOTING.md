# 🔧 Troubleshooting Guide

## Common Issues and Solutions

### 1. Class Not Found Errors

#### Error: "Class 'App\WebSocket\ChatService' not found"

**Cause**: PHP autoloader hasn't been regenerated after adding new classes.

**Solution 1: Regenerate Composer Autoloader (Recommended)**
```bash
composer dump-autoload
```

**Solution 2: Manual Fix (Already Applied)**
The files have been updated to manually require dependencies:
- `WebSocketNotifier.php` now requires `ChatService.php`
- `OrderController.php` now requires both files
- All public pages require `ChatService.php` directly

**Verification**:
```bash
# Check if composer autoload is working
php -r "require 'vendor/autoload.php'; echo 'Autoload OK';"
```

### 2. WebSocket Server Issues

#### Error: "Connection refused" or "WebSocket not connecting"

**Cause**: WebSocket server is not running.

**Solution**:
```bash
# Start the WebSocket server
php websocket-server.php
```

**Verification**:
- Terminal should show: `✓ WebSocket server is running!`
- Browser console should show: `✓ WebSocket connected`

#### Error: "Port 8080 already in use"

**Cause**: Another process is using port 8080.

**Solution 1: Find and stop the process**
```bash
# Windows
netstat -ano | findstr :8080
taskkill /PID <PID> /F

# Linux/Mac
lsof -i :8080
kill -9 <PID>
```

**Solution 2: Change the port**
1. Edit `websocket-server.php` - change port number
2. Edit `public/assets/js/websocket-client.js` - update WebSocket URL

### 3. Database Errors

#### Error: "Table 'seed cycle.chat_conversations' doesn't exist"

**Cause**: WebSocket database tables not imported.

**Solution**:
```bash
mysql -u root -p "seed cycle" < database-websocket-tables.sql
```

**Verification**:
```sql
USE `seed cycle`;
SHOW TABLES LIKE 'chat_%';
-- Should show: chat_conversations, chat_messages
```

#### Error: "Unknown column 'payment_method' in 'field list'"

**Cause**: Database schema is outdated.

**Solution**:
```sql
USE `seed cycle`;
ALTER TABLE orders ADD COLUMN payment_method VARCHAR(20) DEFAULT 'cod';
ALTER TABLE orders ADD COLUMN payment_status VARCHAR(20) DEFAULT 'pending';
```

### 4. Composer Issues

#### Error: "composer: command not found"

**Cause**: Composer is not installed.

**Solution**:
1. Download from [getcomposer.org](https://getcomposer.org/)
2. Install globally
3. Verify: `composer --version`

#### Error: "Your requirements could not be resolved"

**Cause**: Dependency conflicts.

**Solution**:
```bash
# Clear composer cache
composer clear-cache

# Update dependencies
composer update

# Or fresh install
rm -rf vendor composer.lock
composer install
```

### 5. File Permission Errors

#### Error: "Permission denied" when uploading files

**Cause**: Upload directory is not writable.

**Solution**:
```bash
# Windows (via File Explorer)
# Right-click folder → Properties → Security → Edit → Full Control

# Linux/Mac
chmod -R 755 public/assets/uploads
chown -R www-data:www-data public/assets/uploads
```

### 6. Session Issues

#### Error: "Session not starting" or "User not logged in"

**Cause**: Session configuration issues.

**Solution**:
```php
// Check session status
<?php
session_start();
var_dump($_SESSION);
?>
```

**Fix**:
1. Check `php.ini` session settings
2. Ensure session directory is writable
3. Clear browser cookies
4. Check session timeout settings

### 7. Mobile/PWA Issues

#### Issue: PWA not installing on Android

**Cause**: Missing manifest or icons.

**Solution**:
1. Generate PWA icons (see `generate-pwa-icons.md`)
2. Place icons in `public/assets/images/`
3. Verify `manifest.json` paths
4. Check HTTPS is enabled (required for PWA)

#### Issue: Service Worker not registering

**Cause**: HTTPS required or path incorrect.

**Solution**:
1. Use HTTPS or localhost
2. Check service worker path in navbar.php
3. Clear browser cache
4. Check browser console for errors

### 8. WebSocket Notification Issues

#### Issue: Notifications not appearing in real-time

**Cause**: WebSocket server not running or not connected.

**Solution**:
1. Start WebSocket server: `php websocket-server.php`
2. Check browser console for connection status
3. Verify user is authenticated
4. Check database tables exist

#### Issue: Notification badge not updating

**Cause**: JavaScript not loaded or WebSocket not connected.

**Solution**:
1. Check `websocket-client.js` is loaded
2. Verify navbar has `data-user-id` attribute
3. Check browser console for errors
4. Clear browser cache

### 9. Chat Interface Issues

#### Issue: Messages not sending

**Cause**: WebSocket not connected or database error.

**Solution**:
1. Check WebSocket connection status
2. Verify conversation exists
3. Check database permissions
4. Review server terminal for errors

#### Issue: Typing indicator not working

**Cause**: WebSocket message not being sent/received.

**Solution**:
1. Check WebSocket connection
2. Verify conversation ID is correct
3. Check browser console for errors
4. Test with two browsers

### 10. Performance Issues

#### Issue: Slow page loading

**Cause**: Large images or unoptimized assets.

**Solution**:
1. Optimize images (compress, resize)
2. Enable gzip compression
3. Use browser caching
4. Minify CSS/JS files

#### Issue: Chat lagging on mobile

**Cause**: Too many messages or inefficient rendering.

**Solution**:
1. Implement message pagination
2. Limit initial message load
3. Use virtual scrolling
4. Optimize CSS animations

## 🔍 Debugging Tools

### Browser Console
```javascript
// Check WebSocket status
console.log(window.wsClient);
console.log(window.wsClient.ws.readyState);
// 0=CONNECTING, 1=OPEN, 2=CLOSING, 3=CLOSED

// Check user ID
console.log(document.querySelector('[data-user-id]').dataset.userId);

// Test WebSocket send
window.wsClient.send('ping', {});
```

### PHP Debugging
```php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log to file
error_log('Debug message', 3, 'debug.log');

// Dump variables
var_dump($variable);
print_r($array);
```

### MySQL Debugging
```sql
-- Check table structure
DESCRIBE chat_conversations;

-- Check data
SELECT * FROM chat_conversations LIMIT 10;

-- Check indexes
SHOW INDEX FROM chat_messages;

-- Check foreign keys
SELECT * FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_NAME = 'chat_messages';
```

### Network Debugging
```bash
# Check if port is open
telnet localhost 8080

# Check WebSocket connection
wscat -c ws://localhost:8080

# Monitor network traffic
# Use browser DevTools → Network tab
```

## 📞 Getting Help

### Before Asking for Help

1. **Check error messages**: Read the full error message
2. **Check logs**: Browser console, PHP error log, server terminal
3. **Search documentation**: Check README, guides, and this file
4. **Try solutions**: Attempt the solutions listed above
5. **Isolate the issue**: Determine exactly what's not working

### Information to Provide

When reporting an issue, include:
- **Error message**: Full error text
- **Steps to reproduce**: What you did before the error
- **Environment**: OS, PHP version, browser
- **Screenshots**: If applicable
- **Logs**: Browser console, PHP errors, server output

### Useful Commands

```bash
# Check PHP version
php -v

# Check MySQL version
mysql --version

# Check Composer version
composer --version

# Check if WebSocket server is running
netstat -an | grep 8080

# Check PHP errors
tail -f /path/to/php_error.log

# Check Apache/Nginx errors
tail -f /var/log/apache2/error.log
```

## ✅ Quick Fixes Checklist

When something goes wrong, try these in order:

1. [ ] Clear browser cache (Ctrl+Shift+Delete)
2. [ ] Restart WebSocket server
3. [ ] Restart web server (Apache/Nginx)
4. [ ] Run `composer dump-autoload`
5. [ ] Check database tables exist
6. [ ] Verify file permissions
7. [ ] Check PHP error log
8. [ ] Check browser console
9. [ ] Test in incognito mode
10. [ ] Restart MySQL server

## 🎯 Prevention Tips

### Best Practices
- Always run `composer dump-autoload` after adding new classes
- Keep WebSocket server running during development
- Regularly check error logs
- Test on multiple browsers
- Use version control (git)
- Backup database regularly
- Document custom changes

### Development Workflow
1. Make changes
2. Run `composer dump-autoload` if needed
3. Clear browser cache
4. Test functionality
5. Check error logs
6. Commit changes

---

**Still having issues?** Check the main documentation files:
- `README.md` - General documentation
- `WEBSOCKET_INSTALLATION.md` - WebSocket setup
- `MOBILE_ANDROID_GUIDE.md` - Mobile issues
- `TESTING_GUIDE.md` - Testing procedures
