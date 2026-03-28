# Quick Start Guide - Real-Time Notifications

## 🚀 Fast Setup (5 minutes)

### 1. Update .env File

Add these lines to your `.env` file:

```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=12345
PUSHER_APP_KEY=your_key_here
PUSHER_APP_SECRET=your_secret_here
PUSHER_APP_CLUSTER=mt1
VITE_PUSHER_APP_KEY=your_key_here
VITE_PUSHER_APP_CLUSTER=mt1
```

**Option A: Get Free Pusher Account**
- Visit: https://pusher.com
- Sign up for free account
- Go to App Keys
- Copy credentials above

**Option B: Use Local WebSockets (Development)**
```bash
composer require beyondcode/laravel-websockets
php artisan vendor:publish --provider="BeyondCode\LaravelWebSockets\WebSocketsServiceProvider"
php artisan migrate
```

Then in `.env`:
```env
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=12345
PUSHER_APP_KEY=ABCDEFGH
PUSHER_APP_SECRET=secret
PUSHER_APP_CLUSTER=mt1
VITE_PUSHER_APP_KEY=ABCDEFGH
VITE_PUSHER_APP_CLUSTER=mt1
```

### 2. Build Assets

```bash
npm run build
```

For development with auto-reload:
```bash
npm run dev
```

### 3. Run Migrations (if not done)

```bash
php artisan migrate
```

### 4. Start WebSockets Server (if using local WebSockets)

```bash
php artisan websockets:serve
```

Keep this running in a separate terminal.

### 5. Start Laravel Development Server

```bash
php artisan serve
```

## 🧪 Test It Out

### Option 1: Laravel Tinker
```bash
php artisan tinker
```

```php
$notification = App\Models\Notification::create([
    'user_id' => 1,
    'type' => 'test',
    'data' => ['message' => 'Hello! This is a test notification']
]);
\App\Events\NotificationCreated::dispatch($notification);
```

You should see:
- ✅ Notification bell counter increases
- ✅ New notification appears in modal
- ✅ Browser notification appears (if allowed)

### Option 2: Create a Product
In admin panel:
1. Go to Products → Add Product
2. Fill in the form  
3. Submit
4. All users get notified in real-time

### Option 3: Place an Order
As a regular user:
1. Browse products
2. Add to cart
3. Checkout
4. Admins get notified in real-time

## ✨ Key Features

| Feature | Status |
|---------|--------|
| Notification Bell with Badge | ✅ Real-time |
| Notification Modal | ✅ Click to open |
| Unread Indicator | ✅ Blue dot/background |
| Different Icons | ✅ Products, Orders, Warnings |
| Mark All as Read | ✅ One-click action |
| User Dropdown | ✅ Include logout |
| Admin Dashboard | ✅ Metrics & tables |
| User Dashboard | ✅ Clean layout |
| Real-Time Updates | ✅ Via Echo/Pusher |
| Browser Notifications | ✅ When enabled |

## 🔧 Troubleshooting

### Problem: Notification bell not updating
**Solution:**
1. Open browser DevTools (F12)
2. Check Console for errors
3. Verify `BROADCAST_DRIVER` is NOT 'null'
4. Run `npm run build` again

### Problem: Echo not connecting
**Solution:**
1. Check `.env` has correct Pusher credentials
2. Verify internet connection
3. Check browser console: `window.Echo` should exist
4. Look for "error" messages in console

### Problem: Modal not opening
**Solution:**
1. Clear browser cache (Ctrl+Shift+Delete)
2. Verify Alpine.js is loaded: `window.Alpine` in console
3. Rebuild assets: `npm run build`

### Problem: WebSockets not connecting (local development)
**Solution:**
1. Verify WebSockets server is running: `php artisan websockets:serve`
2. Check it's running on http://127.0.0.1:6001
3. Verify firewall allows port 6001

## 📊 System Files Overview

```
📦 Resources
├── js/
│   ├── app.js                    # Import bootstrap-echo
│   ├── bootstrap-echo.js         # Echo listener setup ⭐
│   └── bootstrap.js
└── views/
    ├── layouts/navigation.blade  # Notification UI ⭐
    ├── admin/dashboard.blade     # Admin metrics
    └── user/dashboard.blade      # User overview

📦 App
├── Events/
│   └── NotificationCreated.php   # Broadcast event ⭐
├── Listeners/
│   └── SendNotification.php      # Create & broadcast ⭐
├── Models/
│   └── Notification.php
└── Http/Controllers/
    ├── AdminDashboardController.php
    └── UserDashboardController.php

📦 Config
└── broadcasting.php              # Broadcast config ⭐
```

⭐ = Key files for notification system

## 🎯 After Setup

### Configure Automated Emails (Optional)
```php
// In SendNotification listener
Mail::send('emails.notification', 
    ['notification' => $notification],
    function ($message) use ($user) {
        $message->to($user->email);
    }
);
```

### Add Custom Notifications
```php
use App\Models\Notification;
use App\Events\NotificationCreated;

// From any controller
$notification = Notification::create([
    'user_id' => auth()->id(),
    'type' => 'custom_type',
    'data' => [
        'message' => 'Your message',
        'related_id' => 123
    ]
]);

NotificationCreated::dispatch($notification);
```

### Customize Notification Icons
Edit: `resources/views/layouts/navigation.blade.php`

Find the switch case around line 166:
```blade
@switch($notification->type)
    @case('product') ... @endcase
    @case('order') ... @endcase
    @case('stock') ... @endcase
@endswitch
```

Add your custom types and SVG icons there.

---

## 📞 Support Resources

- **Laravel Broadcasting Docs:** https://laravel.com/docs/broadcasting
- **Pusher Docs:** https://pusher.com/docs
- **Laravel Echo Docs:** https://laravel.com/docs/broadcasting#client-side-installation
- **Alpine.js Docs:** https://alpinejs.dev

---

**✅ Setup Complete!** 

Your notification system is now ready for real-time updates. Enjoy! 🎉
