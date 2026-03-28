# Troubleshooting Guide - Real-Time Notification System

## Common Issues & Solutions

### 1. Notification Bell Not Showing Counter Badge

**Symptoms:**
- Bell icon appears but no red notification count

**Causes & Solutions:**

```
❌ BROADCAST_DRIVER is 'null'
   → Solution: Change in .env to BROADCAST_DRIVER=pusher

❌ npm assets not rebuilt
   → Solution: Run `npm run build`

❌ Alpine store not initialized
   → Solution: Check browser console for errors related to Alpine

❌ No unread notifications
   → Solution: Create a test notification via Tinker
```

**Test:**
```bash
# In browser console (F12)
window.Alpine.store('notifications')  # Should show { count: X, list: [...] }
```

---

### 2. Echo Connection Issues

**Symptoms:**
- Real-time updates not working
- Browser console shows connection errors
- "Failed to connect" messages

**Causes & Solutions:**

```
❌ Pusher credentials are invalid
   → Solution: Verify credentials in .env and Pusher dashboard

❌ Wrong Pusher cluster
   → Solution: Check PUSHER_APP_CLUSTER matches your Pusher app

❌ Firewall blocking Pusher
   → Solution: Check firewall allows outbound connections to Pusher

❌ VITE variables not set
   → Solution: Rebuild with `npm run build`
```

**Test:**
```javascript
// In browser console
window.Echo  // Should exist and be ready
window.Pusher  // Should exist
console.log(Echo.connector)  // Should show connection details
```

---

### 3. Notification Modal Not Opening

**Symptoms:**
- Click bell but nothing happens
- No error in console

**Causes & Solutions:**

```
❌ Alpine not loaded
   → Solution: Check <script> tag imports Alpine in layout

❌ showNotifications not defined in Alpine data
   → Solution: Verify navigation.blade.php has x-data="{ showNotifications: false }"

❌ Modal HTML not in DOM
   → Solution: Verify notification modal HTML exists in navigation.blade.php

❌ CSS not loading
   → Solution: Run `npm run build` and clear browser cache (Ctrl+Shift+Backspace)
```

**Test:**
```javascript
// In browser console
window.Alpine  // Should exist
document.querySelector('[x-data*="showNotifications"]')  // Should return element
```

---

### 4. WebSockets Server Connection (Local Development)

**Symptoms:**
- WebSockets not working
- "Connection refused" errors
- localhost:6001 not accessible

**Causes & Solutions:**

```
❌ WebSockets server not running
   → Solution: Start with `php artisan websockets:serve`

❌ Port 6001 already in use
   → Solution: Kill process using port OR change WebSockets config

❌ Firewall blocking port 6001
   → Solution: Allow port 6001 in firewall

❌ Wrong broadcast driver
   → Solution: For local WebSockets, BROADCAST_DRIVER must be 'pusher'
```

**Check Status:**
```bash
# Check if port 6001 is listening
lsof -i :6001  # macOS/Linux
netstat -ano | findstr :6001  # Windows

# Kill process if needed
kill -9 <PID>  # macOS/Linux
taskkill /PID <PID> /F  # Windows
```

---

### 5. Notifications Not Being Created

**Symptoms:**
- No notifications appear even after user action
- Beetle icon stays empty
- No database records

**Causes & Solutions:**

```
❌ Event listener not firing
   → Solution: Verify EventServiceProvider has events registered

❌ Notifications table doesn't exist
   → Solution: Run `php artisan migrate`

❌ User doesn't exist
   → Solution: Verify user_id exists in users table

❌ User not authenticated properly
   → Solution: Check auth()->id() returns valid ID
```

**Debug:**
```php
// In Tinker
php artisan tinker
>>> App\Models\Notification::count()  # See how many exist
>>> App\Models\Notification::latest()->first()  # See latest
>>> auth()->user()  # Verify authenticated user
```

---

### 6. Real-Time Updates Not Working (Notification Gate)

**Symptoms:**
- Notification created but doesn't broadcast
- Modal doesn't update in real-time

**Causes & Solutions:**

```
❌ Gate::define('view-notification') not authorized
   → Solution: Laravel has default authorization, verify user can view

❌ NotificationCreated event not being dispatched
   → Solution: Check SendNotification listener calls NotificationCreated::dispatch()

❌ Listener uses wrong queue
   → Solution: Verify listener extends ShouldQueue properly

❌ Database out of sync with broadcast
   → Solution: Refresh/reload notification modal
```

**Verify:**
```php
// In Tinker
$notif = App\Models\Notification::latest()->first();
event(new App\Events\NotificationCreated($notif));  # Manually dispatch
```

---

### 7. User Dropdown Not Working

**Symptoms:**
- Cannot click user dropdown
- Logout button doesn't work

**Causes & Solutions:**

```
❌ x-dropdown component not loaded
   → Solution: Verify x-dropdown component exists in resources/views/components

❌ Forms component not working
   → Solution: Verify form is being submitted properly

❌ Logout route not configured
   → Solution: Check routes/auth.php has logout route
```

**Test:**
```javascript
// In browser console
document.querySelector('[x-data*="open"]')  // Should find dropdown element
window.Alpine.data('dropdown')  // Check Alpine data
```

---

### 8. Dashboard Metrics Not Showing

**Symptoms:**
- Metric cards appear but show wrong numbers
- Admin dashboard incomplete

**Causes & Solutions:**

```
❌ Database queries returning null
   → Solution: Verify database has products/orders

❌ Controller not passing data to view
   → Solution: Check AdminDashboardController returns compact() properly

❌ View trying to access undefined variables
   → Solution: Check all variables are being passed from controller
```

**Debug:**
```bash
# In browser DevTools Network tab, check /admin/dashboard response
# Response should have HTML with metric values
```

---

### 9. CSS/Styling Issues

**Symptoms:**
- Modal looks broken
- Button colors wrong
- Layout not responsive

**Causes & Solutions:**

```
❌ Tailwind CSS not compiled
   → Solution: Run `npm run build`

❌ Using dev server without watcher
   → Solution: Run `npm run dev` instead of build

❌ Cached CSS in browser
   → Solution: Clear cache (Ctrl+Shift+Delete) and hard refresh (Ctrl+F5)
```

---

## Debugging Checklist

When something isn't working, go through this:

1. **Browser Console (F12)**
   - Any error messages?
   - `window.Echo` exists?
   - `window.Alpine` exists?

2. **Network Tab (F12 → Network)**
   - Any failed requests?
   - Check `/broadcasting/auth` response

3. **Laravel Log**
   ```bash
   tail -f storage/logs/laravel.log
   ```

4. **Database**
   ```bash
   php artisan tinker
   >>> App\Models\Notification::latest()->first()
   ```

5. **Queue/Jobs** (if using queues)
   ```bash
   php artisan queue:work
   ```

6. **Clear Everything**
   ```bash
   php artisan cache:clear
   npm run build
   # Hard refresh browser: Ctrl+Shift+Delete
   ```

---

## Performance Tuning

If notifications are slow:

```php
// In SendNotification listener - use bulk insert
$notifications = [];
foreach ($users as $user) {
    $notifications[] = [
        'user_id' => $user->id,
        'type' => 'product_added',
        'data' => json_encode([...]),
        'created_at' => now(),
        'updated_at' => now(),
    ];
}
Notification::insert($notifications);
```

---

## Testing in Different Scenarios

### Test 1: As Admin User
1. Log in as admin
2. Create product → Should see notification
3. Check other admins see it too

### Test 2: As Regular User  
1. Log in as user
2. Place order → Admin should get notified
3. Admin updates order → User should be notified

### Test 3: Multiple Tabs
1. Open app in 2 browser tabs
2. Create notification in one
3. Verify it appears in both tabs in real-time

### Test 4: Browser Notifications
1. Allow browser notifications when prompted
2. Create notification
3. Verify browser notification appears

---

## Getting Help

If still stuck:

1. Check logs: `storage/logs/laravel.log`
2. Read NOTIFICATION_SETUP.md for comprehensive guide
3. Verify all files created: Run `verify-setup.sh`
4. Check Pusher dashboard for connection status
5. Review Laravel Broadcasting docs: https://laravel.com/docs/broadcasting

---

**Last Resort:**
```bash
# Reset everything
php artisan cache:clear
php artisan config:clear
php artisan route:clear
npm run build
php artisan serve
```

Then test again from scratch.
