<div class="grid-4">
    @foreach ($comp->getSercTanks()->groupBy('serc_tank')->sortKeys() as $ind => $tank)
        <div class="card">
            <h4>Tank {{ $ind }}</h4>
            <ol class=" list-decimal list-inside">
                @foreach ($tank->sortBy('serc_order') as $competitor)
                    <li class="list-item">{{ $competitor->team }}

                    </li>
                @endforeach
            </ol>
        </div>
    @endforeach
</div>
