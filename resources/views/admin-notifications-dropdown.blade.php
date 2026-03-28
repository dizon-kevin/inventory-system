<div
    class="notifications-menu"
    data-notifications-menu
    data-fetch-url="{{ $notificationsFetchUrl ?? (auth()->user()?->isAdmin() ? route('admin.notifications.index') : route('user.notifications.index')) }}"
    data-mark-read-url="{{ $notificationsMarkAllReadUrl ?? route('notifications.mark-all-read') }}"
>
    <button class="topbar-icon-btn notifications-trigger" type="button" aria-label="Notifications">
        <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
            <path d="M13.73 21a2 2 0 01-3.46 0"/>
        </svg>
        <span class="notifications-badge" data-notifications-badge>0</span>
    </button>

    <div class="notifications-dropdown" data-notifications-dropdown>
        <div class="notifications-dropdown-header">
            <div>
                <p class="notifications-dropdown-title">Notifications</p>
                <p class="notifications-dropdown-subtitle" data-notifications-subtitle>No new notifications</p>
            </div>
            <button class="notifications-mark-all" type="button" data-notifications-mark-all>
                Mark all as read
            </button>
        </div>

        <div class="notifications-list" data-notifications-list>
            <div class="notifications-empty">No notifications yet.</div>
        </div>
    </div>
</div>
