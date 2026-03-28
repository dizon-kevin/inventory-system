<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Notifications') }}</h2>
            <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                @csrf
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Mark all as read</button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6 space-y-3">
                @forelse($notifications as $notification)
                    <div class="p-3 rounded border @if(!$notification->read_at) bg-yellow-50 border-yellow-200 @else bg-gray-50 border-gray-200 @endif">
                        <p class="text-gray-700 font-semibold">{{ $notification->title }}</p>
                        <p class="text-sm text-gray-600">{{ $notification->description }}</p>
                        <p class="text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">No notifications yet.</p>
                @endforelse
            </div>

            <div class="mt-4">{{ $notifications->links() }}</div>
        </div>
    </div>
</x-app-layout>
