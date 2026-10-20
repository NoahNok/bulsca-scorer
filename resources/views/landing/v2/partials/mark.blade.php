{{-- The favicon's "S." mark. With $live, its dot pulses. --}}
<svg class="v2-mark" width="{{ $size }}" height="{{ $size }}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" aria-hidden="true">
    <rect width="64" height="64" rx="14" fill="#111827" />
    <path d="M40 20.5C37 16 30 15 25.5 16.5S18 22 19.5 26.5C21.5 32 37.5 31 39.5 38.5C41 44 36 48.5 29.5 48.5C24 48.5 19.5 46 17.5 42.5"
        fill="none" stroke="#fff" stroke-width="8" stroke-linecap="round" />
    <circle cx="49" cy="45" r="5.5" fill="#00d5be" @class(['v2-mark-dot', 'is-live' => $live ?? false]) style="transform-box: fill-box; transform-origin: center" />
</svg>
