# Notification Server Restart Instructions

## Issue
Laravel is getting 404 "Cannot POST /api/notify" because the server is running old code.

## Solution

### Step 1: Verify the updated code is on the server
Make sure `notification_server_corrected.js` is in the server directory (usually `/var/www/html/notification_server` or similar).

### Step 2: Restart PM2
```bash
# Navigate to the notification server directory
cd /var/www/html/notification_server

# Restart the PM2 process
pm2 restart noti

# Or if you need to reload with the new code
pm2 delete noti
pm2 start server.js --name noti

# Check status
pm2 status

# View logs
pm2 logs noti
```

### Step 3: Test the endpoint
```bash
# Test if the server is accessible
curl https://maxwin688.site/api/test

# Should return:
# {"success":true,"message":"Notification server is running","online_users":X,"timestamp":"..."}
```

### Step 4: Test notification endpoint
```bash
# Test POST to /api/notify
curl -X POST https://maxwin688.site/api/notify \
  -H "Content-Type: application/json" \
  -d '{
    "to_user_id": "public",
    "title": "Test Notification",
    "body": "This is a test",
    "notification_data": {
      "route": "/test",
      "type": "test"
    }
  }'
```

### Step 5: Check PM2 logs
After restarting, check the logs to see if the server started correctly:
```bash
pm2 logs noti --lines 50
```

You should see:
- `🚀 Notification Server running on port 3000`
- `📡 HTTP endpoint: http://localhost:3000/api/notify`
- `🔌 Socket.IO endpoint: http://localhost:3000`

### Step 6: Test from Laravel
After restarting, create a post/lesson/dhamma talk from the admin panel and check:
1. **Laravel logs** (`storage/logs/laravel.log`): Should show `✅ Notification sent successfully`
2. **PM2 logs** (`pm2 logs noti`): Should show `📥 HTTP POST /api/notify received` and `📢 Broadcasting notification to all users`

## Nginx Configuration
Your nginx config looks correct - it proxies all requests to `http://localhost:3000`. No changes needed.

## Troubleshooting

### If still getting 404:
1. Check if PM2 is actually running the updated file:
   ```bash
   pm2 show noti
   # Check the "script path" - should point to notification_server_corrected.js
   ```

2. Check if the server is listening on port 3000:
   ```bash
   netstat -tulpn | grep 3000
   # or
   lsof -i :3000
   ```

3. Check nginx error logs:
   ```bash
   tail -f /var/log/nginx/socket-maxwin688site.error.log
   ```

### If server restarts but still doesn't work:
1. Make sure the file name matches what PM2 is running
2. Check for syntax errors in the server file:
   ```bash
   node notification_server_corrected.js
   # Should start without errors
   ```

