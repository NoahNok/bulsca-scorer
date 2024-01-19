<div class="card">
    <h3>Competed at</h3>
    <ol class=" columns-3">
        @foreach ($data as $comp)
            <li><a href="{{ route('public.results.comp', $comp->name . '.' . $comp->id) }}"
                    class="link  whitespace-nowrap">
                    {{ $comp->name }}
                </a></li>
        @endforeach
</div>