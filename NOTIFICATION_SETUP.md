# Real-Time Notification System Setup Guide

## Overview
This guide explains the complete real-time notification system implementation using Laravel Echo, Pusher, and WebSockets.

## Files Created/Modified

### 1. Configuration Files
- **config/broadcasting.php** - Broadcasting configuration for Pusher/Echo
- **resources/js/bootstrap-echo.js** - Laravel Echo listener setup
- **resources/js/app.js** - Updated to import Echo bootstrap

### 2. Events
- **app/Events/NotificationCreated.php** - Broadcast event for notifications

### 3. Listeners
- **app/Listeners/SendNotification.php** - Updated to dispatch NotificationCreated event

### 4. Views
- **resources/views/layouts/navigation.blade.php** - Enhanced notification modal
- **resources/views/admin/dashboard.blade.php** - Admin dashboard (already optimized)
- **resources/views/user/dashboard.blade.php** - User dashboard (already optimized)

## Environment Setup

### 1. Install Pusher/WebSockets
Choose one option below:

#### Option A: Using Pusher (Recommended for production)
```bash
npm install pusher-js laravel-echo
```

Add to `.env`:
```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY=your_app_key
VITE_PUSHER_APP_CLUSTER=mt1
```

Get credentials from: https://pusher.com

#### Option B: Using Laravel WebSockets (For local development)
```bash
composer require beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider"
php artisan migrate
```

Add to `.env`:
```
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=12345
PUSHER_APP_KEY=ABCDEFGH
PUSHER_APP_SECRET=secret
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY=ABCDEFGH
VITE_PUSHER_APP_CLUSTER=mt1
```

Start WebSockets server:
```bash
php artisan websockets:serve
```

### 2. Build Frontend Assets
```bash
npm run build
# or for development with watch:
npm run dev
```

## Key Features Implemented

### ✅ Real-Time Notifications
- Notification bell in header with counter badge
- Counter updates in real-time using Laravel Echo
- Browser notifications when allowed

### ✅ Notification Modal
- Click bell icon to open modal (not separate page)
- Shows recent notifications (last 20)
- Unread notifications highlighted in blue with indicator dot
- Different icons for different notification types:
  - 📦 Product notifications (green)
  - 📋 Order notifications (blue)  
  - ⚠️ Stock alerts (yellow)
  - 🔔 General notifications (gray)
- Timestamp shown as "2 hours ago" format
- Mark all as read button
- Responsive design with scrollable content

### ✅ User Dropdown
- Click username in top-right to open dropdown
- Profile link
- Logout button (no separate page)
- Consistent styling

### ✅ Dashboard Layout
**Admin Dashboard:**
- 4 metric cards: Total Products, Low Stock Items, Total Stock Value, Total Orders
- Recent Products table with details
- Recent Orders table with status tracking
- Clean, minimal design

**User Dashboard:**
- 3 metric cards: Total Products, My Orders, Cart Items
- Available Products table
- Quick action buttons: Cart, Order History
- Responsive design

### ✅ Notification Triggers
Notifications are created and broadcast for:
1. **Product Added** - All users notified
2. **Product Updated** - All users notified
3. **Product Low Stock** - All users notified
4. **Order Placed** - All admins notified
5. **Order Status Updated** - Customer notified

## Usage

### Creating Notifications Manually

```php
use App\Models\Notification;
use App\Events\NotificationCreated;

$notification = Notification::create([
    'user_id' => $userId,
    'type' => 'custom',
    'data' => [
        'message' => 'Your custom message here',
        'product_id' => $productId // optional
    ],
]);

// Broadcast to user in real-time
NotificationCreated::dispatch($notification);
```

### Marking Notifications as Read

**Via Form (in views):**
```blade
<form action="{{ route('notifications.read') }}" method="POST">
    @csrf
    <button type="submit">Mark all as read</button>
</form>
```

**Via Controller:**
```php
Notification::where('user_id', auth()->id())
    ->whereNull('read_at')
    ->update(['read_at' => now()]);
```

## Testing the System

### 1. Test Notifications Creation
```bash
php artisan tinker
>>> $notification = App\Models\Notification::create([
...   'user_id' => 1,
...   'type' => 'test',
...   'data' => ['message' => 'Test notification']
... ]);
>>> \App\Events\NotificationCreated::dispatch($notification);
```

### 2. Test Real-Time Updates
- Open application in browser
- Open browser console (F12)
- Create a notification in tinker
- Observe:
  - Notification counter increases
  - New notification appears in modal
  - Browser notification appears (if allowed)

### 3. Manual Testing Checklist
- [ ] Notification bell shows count badge
- [ ] Click bell opens modal
- [ ] Modal is scrollable if more than 5 notifications
- [ ] Unread notifications show blue dot and background
- [ ] Timestamps display correctly
- [ ] Mark all as read button works
- [ ] Modal closes on ESC key
- [ ] Dropdown menu appears when clicking username
- [ ] Logout button in dropdown works
- [ ] Admin dashboard shows all metrics
- [ ] User dashboard shows product table
- [ ] Real-time updates work when new notification is created

## Troubleshooting

### Notifications Not Appearing
1. Check `.env` has correct Pusher/WebSocket credentials
2. Verify `BROADCAST_DRIVER` is set to 'pusher' (not 'null')
3. Run `npm run build` after changes
4. Check browser console for errors
5. Verify NotificationCreated event is being dispatched

### Modal Not Opening
1. Check Alpine.js is loaded properly
2. Verify `showNotifications` Alpine component exists in navigation.blade.php
3. Check browser console for JavaScript errors

### Real-Time Updates Not Working
1. Verify Pusher connection in browser console: `window.Echo`
2. Check user is authenticated
3. Verify `data-user-id` attribute exists on nav element
4. Check notification store is initialized in Alpine

### Dropdown Not Working
1. Verify x-dropdown component exists
2. Check Tailwind CSS is compiled
3. Verify dropdown-link component is available

## Architecture Diagram

```
User Browser
    ↓
Laravel Echo (Listening to private channel user.{id})
    ↓
Pusher/WebSockets (Broadcasting)
    ↓
← NotificationCreated Event dispatched from Listener
← SendNotification Listener creates DB record
← ProductAdded, OrderPlaced, etc events trigger notifications
```

## File Summary

| File | Purpose |
|------|---------|
| config/broadcasting.php | Configure broadcasting driver |
| resources/js/bootstrap-echo.js | Setup Echo listeners |
| app/Events/NotificationCreated.php | Event for notification broadcast |
| app/Listeners/SendNotification.php | Handle system events & create notifications |
| resources/views/layouts/navigation.blade.php | Notification UI & modal |
| app/Models/Notification.php | Database model |
| routes/web.php | Routes for notifications |

## Next Steps

1. Set up Pusher account or configure WebSockets
2. Update .env with credentials
3. Run `npm run build`
4. Test the system following the testing checklist above
5. Deploy to production

---
**Last Updated:** March 21, 2026
