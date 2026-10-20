{{-- Connection pill and clock, driven by $store.live --}}
<div class="flex items-center gap-4 {{ $class ?? '' }}">
    <div class="flex items-center gap-2">
        <span class="size-2.5 rounded-full animate-pulse"
            :class="$store.live.connected === null ? 'bg-amber-400' : ($store.live.connected ? 'bg-green-500' : 'bg-red-500')"></span>
        <span class="font-semibold text-gray-700 {{ $pillClass ?? 'text-sm' }}"
            x-text="$store.live.connected === null ? 'Connecting…' : ($store.live.connected ? 'Live' : 'Lost connection, retrying…')">Connecting…</span>
    </div>
    <span class="font-archivo font-semibold tabular-nums {{ $clockClass ?? 'text-2xl' }}" x-text="$store.live.clock($store.live.now)"></span>
</div>
