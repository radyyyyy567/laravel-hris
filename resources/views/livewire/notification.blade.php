<div class="space-y-4">
    <h1 class="text-xl font-semibold">Notifikasi</h1>

    @forelse ($notifications as $notification)
        <div class="bg-white rounded-lg shadow p-4">
            <div class="font-bold capitalize">{{ $notification->type }}</div>
            <div class="text-sm text-gray-600">
                {{ $notification->description ?? '-' }}
            </div>
            <div class="text-xdwads text-gray-400">
                {{ $notification->created_at->diffForHumans() }}
            </div>
        </div>
    @empty
        <p>Tidak ada notifikasi.</p>
    @endforelse
</div>