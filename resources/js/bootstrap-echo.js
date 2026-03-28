import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
});

// Listen for notifications on document ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupNotificationListeners);
} else {
    setupNotificationListeners();
}

function setupNotificationListeners() {
    // Set user ID from data attribute
    const navElement = document.querySelector('[data-user-id]');
    if (!navElement) return;
    
    const userId = navElement.dataset.userId;
    
    // Listen to private channel
    if (window.Echo) {
        window.Echo.private(`user.${userId}`)
            .listen('NotificationCreated', (e) => {
                // Update notification count
                if (window.Alpine && window.Alpine.store('notifications')) {
                    const current = window.Alpine.store('notifications').count || 0;
                    window.Alpine.store('notifications').count = current + 1;
                    
                    // Add to notifications list 
                    window.Alpine.store('notifications').list.unshift(e.notification);
                    
                    // Keep only 20 notifications
                    if (window.Alpine.store('notifications').list.length > 20) {
                        window.Alpine.store('notifications').list.pop();
                    }
                }
                
                // Dispatch custom event for modal refresh
                window.dispatchEvent(new CustomEvent('notification-received', {
                    detail: e.notification
                }));
                
                // Browser notification
                if ('Notification' in window && Notification.permission === 'granted') {
                    new Notification(e.notification.data.message || 'New Notification', {
                        icon: '/images/logo.png',
                        tag: 'inventory-notification',
                        requireInteraction: false,
                    });
                }
            });
    }
}

// Request notification permission
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}

