@props(['comp', 'org' => null, 'url' => '#'])
<a href="{{ $url }}" class="se-card se-card-hover">
    <div class="se-card-body flex items-center justify-between">
        <h3 class="-mb-1!">{{ $comp->name }}</h3>
        @if ($org != null)
            <img src="{{ $org->getLogo() }}" alt="" class="size-8 rounded-full">
        @endif

    </div>

    <div class="bg-gray-200 px-4 py-2 ">
        <p class="font-archivo text-xs! text-gray-700! uppercase -mb-1">{{ $comp->when->format('M jS Y') }}</p>
    </div>
</a>
