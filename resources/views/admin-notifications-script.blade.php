<script>
    (function () {
        if (window.__storixNotificationsInitialized) return;
        window.__storixNotificationsInitialized = true;

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function relativeTime(value) {
            if (!value) return 'Just now';

            const date = new Date(value);
            if (Number.isNaN(date.getTime())) return 'Just now';

            const diffSeconds = Math.max(1, Math.floor((Date.now() - date.getTime()) / 1000));
            const ranges = [
                { limit: 60, unit: 's', value: 1 },
                { limit: 3600, unit: 'm', value: 60 },
                { limit: 86400, unit: 'h', value: 3600 },
                { limit: 604800, unit: 'd', value: 86400 },
                { limit: 2592000, unit: 'w', value: 604800 },
                { limit: 31536000, unit: 'mo', value: 2592000 },
                { limit: Infinity, unit: 'y', value: 31536000 }
            ];

            const range = ranges.find(function (item) {
                return diffSeconds < item.limit;
            });

            return Math.floor(diffSeconds / range.value) + range.unit + ' ago';
        }

        function notificationMeta(type) {
            const normalized = String(type || '').toLowerCase();

            if (normalized === 'new_order') {
                return {
                    color: '#00d4aa',
                    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6h15l-1.5 8.5H8L6 4H3"/><circle cx="9" cy="19" r="1.5"/><circle cx="18" cy="19" r="1.5"/></svg>'
                };
            }

            if (normalized === 'order_approved') {
                return {
                    color: '#00a878',
                    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>'
                };
            }

            if (normalized === 'order_rejected') {
                return {
                    color: '#dc2626',
                    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>'
                };
            }

            if (normalized === 'order_completed') {
                return {
                    color: '#2563eb',
                    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-6"/></svg>'
                };
            }

            if (normalized.includes('user')) {
                return {
                    color: '#7c3aed',
                    icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'
                };
            }

            return {
                color: '#2563eb',
                icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-6"/></svg>'
            };
        }

        function renderNotifications(listEl, notifications) {
            if (!notifications.length) {
                listEl.innerHTML = '<div class="notifications-empty">No notifications yet.</div>';
                return;
            }

            listEl.innerHTML = notifications.map(function (notification) {
                const meta = notificationMeta(notification.type);
                const unread = !notification.read_at;

                return '' +
                    '<div class="notifications-item' + (unread ? ' is-unread' : '') + '">' +
                        '<span class="notifications-unread-dot' + (unread ? '' : ' is-read') + '"></span>' +
                        '<span class="notifications-item-icon" style="background:' + meta.color + ';">' + meta.icon + '</span>' +
                        '<div class="notifications-item-body">' +
                            '<p class="notifications-item-title">' + escapeHtml(notification.title) + '</p>' +
                            '<p class="notifications-item-description">' + escapeHtml(notification.description) + '</p>' +
                        '</div>' +
                        '<span class="notifications-item-time">' + escapeHtml(relativeTime(notification.created_at)) + '</span>' +
                    '</div>';
            }).join('');
        }

        function updateBadge(menu, unreadCount, shouldShake) {
            const badge = menu.querySelector('[data-notifications-badge]');
            const subtitle = menu.querySelector('[data-notifications-subtitle]');
            const markAllButton = menu.querySelector('[data-notifications-mark-all]');
            const trigger = menu.querySelector('.notifications-trigger');

            badge.textContent = unreadCount > 99 ? '99+' : String(unreadCount);
            badge.classList.toggle('is-visible', unreadCount > 0);
            subtitle.textContent = unreadCount > 0
                ? unreadCount + ' unread notification' + (unreadCount === 1 ? '' : 's')
                : 'No new notifications';
            markAllButton.disabled = unreadCount === 0;

            if (shouldShake) {
                trigger.classList.remove('is-shaking');
                void trigger.offsetWidth;
                trigger.classList.add('is-shaking');
                window.setTimeout(function () {
                    trigger.classList.remove('is-shaking');
                }, 600);
            }
        }

        function initMenu(menu) {
            const trigger = menu.querySelector('.notifications-trigger');
            const listEl = menu.querySelector('[data-notifications-list]');
            const markAllButton = menu.querySelector('[data-notifications-mark-all]');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const fetchUrl = menu.getAttribute('data-fetch-url');
            const markReadUrl = menu.getAttribute('data-mark-read-url');

            let unreadCount = 0;
            let isLoading = false;

            trigger.addEventListener('click', function (event) {
                event.stopPropagation();
                menu.classList.toggle('open');
            });

            document.addEventListener('click', function (event) {
                if (!menu.contains(event.target)) {
                    menu.classList.remove('open');
                }
            });

            markAllButton.addEventListener('click', async function () {
                if (markAllButton.disabled) return;

                markAllButton.disabled = true;

                const items = Array.from(listEl.querySelectorAll('.notifications-item'));
                items.forEach(function (item) {
                    item.classList.remove('is-unread');
                    const dot = item.querySelector('.notifications-unread-dot');
                    if (dot) dot.classList.add('is-read');
                });

                updateBadge(menu, 0, false);

                try {
                    const response = await fetch(markReadUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({})
                    });

                    if (!response.ok) throw new Error('Failed to mark notifications as read.');
                    unreadCount = 0;
                } catch (error) {
                    console.error(error);
                    fetchNotifications(false);
                }
            });

            async function fetchNotifications(isInitialLoad) {
                if (isLoading) return;
                isLoading = true;

                try {
                    const response = await fetch(fetchUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Failed to load notifications.');
                    }

                    const payload = await response.json();
                    const nextUnreadCount = Number(payload.unread_count || 0);

                    renderNotifications(listEl, Array.isArray(payload.notifications) ? payload.notifications : []);
                    updateBadge(menu, nextUnreadCount, !isInitialLoad && nextUnreadCount > unreadCount);
                    unreadCount = nextUnreadCount;
                } catch (error) {
                    console.error(error);
                } finally {
                    isLoading = false;
                }
            }

            fetchNotifications(true);
            window.setInterval(function () {
                fetchNotifications(false);
            }, 15000);
        }

        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-notifications-menu]').forEach(initMenu);
        });
    })();
</script>
