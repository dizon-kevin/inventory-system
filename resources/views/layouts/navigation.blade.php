@php $notifications = \App\Models\Notification::where('user_id', auth()->id())->latest()->take(20)->get(); @endphp

<div x-data="{ open: false, showNotifications: false }" @keydown.escape="open = false; showNotifications = false" data-user-id="{{ auth()->id() }}">
<nav class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @if(auth()->user()->isAdmin())
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                            {{ __('Products') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                            {{ __('Categories') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                            {{ __('Orders') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                            {{ __('Users') }}
                        </x-nav-link>
                        <x-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                            {{ __('Reports') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                        <x-nav-link :href="route('user.products.index')" :active="request()->routeIs('user.products.*')">
                            {{ __('Products') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <!-- Notification Bell -->
                <div class="relative">
                    <button @click="showNotifications = true" class="relative inline-flex items-center justify-center w-10 h-10 text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 rounded-md hover:bg-gray-100" title="Notifications">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V7a5 5 0 00-10 0v5l-5 5h5m0 0v1a3 3 0 006 0v-1m-6 0h6"></path>
                        </svg>
                        <span x-show="$store.notifications.count > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1 -translate-y-1 bg-red-600 rounded-full" x-text="$store.notifications.count"></span>
                    </button>
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <span>{{ Auth::user()->name }}</span>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <hr class="border-gray-200">

                        <!-- Authentication - Log Out -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <!-- Mobile Notification Bell -->
        <div class="px-4 py-3 border-b border-gray-100">
            <button @click="showNotifications = true" class="w-full inline-flex items-center justify-between px-3 py-2 border border-transparent text-base leading-4 font-medium rounded-md text-gray-500 bg-gray-50 hover:text-gray-700 hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150">
                <span class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V7a5 5 0 00-10 0v5l-5 5h5m0 0v1a3 3 0 006 0v-1m-6 0h6"></path>
                    </svg>
                    Notifications
                </span>
                <span x-show="$store.notifications.count > 0" class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold text-white bg-red-600 rounded-full" x-text="$store.notifications.count"></span>
            </button>
        </div>

        <div class="pt-2 pb-3 space-y-1">
            @if(auth()->user()->isAdmin())
                <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                    {{ __('Products') }}
                </x-responsive-nav-link>                <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                    {{ __('Orders') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                    {{ __('Users') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                    {{ __('Categories') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('admin.reports')" :active="request()->routeIs('admin.reports')">
                    {{ __('Reports') }}
                </x-responsive-nav-link>
            @else
                <x-responsive-nav-link :href="route('user.dashboard')" :active="request()->routeIs('user.dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('user.products.index')" :active="request()->routeIs('user.products.*')">
                    {{ __('Products') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4 py-3 bg-gray-50 rounded-lg">
                <div class="font-bold text-base text-gray-900">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-600">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Notifications Modal -->
<div x-show="showNotifications" class="fixed inset-0 z-50 overflow-y-auto" @click.away="showNotifications = false" style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="showNotifications" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showNotifications = false"></div>

        <!-- Modal -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Header -->
            <div class="bg-white px-4 pt-5 pb-4 border-b border-gray-200 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Notifications
                        </h3>
                        @php
                            $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count();
                        @endphp
                        @if($unreadCount > 0)
                            <p class="text-sm text-gray-500 mt-1">{{ $unreadCount }} unread</p>
                        @endif
                    </div>
                    <button @click="showNotifications = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Notifications List -->
            <div class="bg-white px-4 py-5 sm:p-6 max-h-96 overflow-y-auto">
                @if($notifications->count() > 0)
                    <ul class="space-y-3">
                        @foreach($notifications as $notification)
                            <li class="relative p-4 {{ is_null($notification->read_at) ? 'bg-blue-50 border-l-4 border-blue-500' : 'bg-gray-50' }} border border-gray-200 rounded-lg hover:shadow-md transition">
                                <!-- Unread indicator -->
                                @if(is_null($notification->read_at))
                                    <div class="absolute top-3 right-3">
                                        <span class="inline-flex h-3 w-3 rounded-full bg-blue-500"></span>
                                    </div>
                                @endif
                                
                                <!-- Notification type icon -->
                                <div class="flex gap-3">
                                    <div class="flex-shrink-0 mt-0.5">
                                        @switch($notification->type)
                                            @case('product')
                                                <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                                @break
                                            @case('order')
                                                <svg class="h-5 w-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V5a1 1 0 00-1-1H5a1 1 0 00-1 1v14a1 1 0 001 1h5m5-8a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                @break
                                            @case('stock')
                                                <svg class="h-5 w-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                </svg>
                                                @break
                                            @default
                                                <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V7a5 5 0 00-10 0v5l-5 5h5m0 0v1a3 3 0 006 0v-1m-6 0h6"></path>
                                                </svg>
                                        @endswitch
                                    </div>

                                    <!-- Notification content -->
                                    <div class="flex-1">
                                        <p class="text-sm {{ is_null($notification->read_at) ? 'font-bold text-gray-900' : 'font-medium text-gray-700' }}">
                                            {{ $notification->data['message'] ?? 'Notification' }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5-5V7a5 5 0 00-10 0v5l-5 5h5m0 0v1a3 3 0 006 0v-1m-6 0h6" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-500">No notifications yet</p>
                    </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 border-t border-gray-200">
                <div class="flex gap-3 justify-between">
                    @if($notifications->count() > 0)
                        <form action="{{ route('user.notifications.read') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-600 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                Mark all as read
                            </button>
                        </form>
                    @endif
                    
                    <button @click="showNotifications = false" type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('notifications', { 
        count: {{ \App\Models\Notification::where('user_id', auth()->id())->whereNull('read_at')->count() }}, 
        list: @json($notifications) 
    });
});

if (window.Echo) {
    window.Echo.channel('notifications')
        .listen('.product.added', (e) => {
            if (Alpine.store('notifications')) {
                Alpine.store('notifications').count++;
            }
        })
        .listen('.product.updated', (e) => {
            if (Alpine.store('notifications')) {
                Alpine.store('notifications').count++;
            }
        })
        .listen('.product.low-stock', (e) => {
            if (Alpine.store('notifications')) {
                Alpine.store('notifications').count++;
            }
        })
        .listen('.order.placed', (e) => {
            if (Alpine.store('notifications')) {
                Alpine.store('notifications').count++;
            }
        })
        .listen('.order.status-updated', (e) => {
            if (Alpine.store('notifications')) {
                Alpine.store('notifications').count++;
            }
        });
}
</script>
</div>
