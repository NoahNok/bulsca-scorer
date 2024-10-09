<div class="grid-4">
    @foreach ($comp->getSercTanks()->groupBy('serc_tank')->sortKeys() as $ind => $tank)
        <div class="card">
            <h4>Tank {{ $ind }}</h4>
            <ol class=" list-decimal list-inside">
                @foreach ($tank->sortBy('serc_order') as $competitor)
                    @php
                        $team = App\Models\Competitor::find($competitor->tid);
                        $name = $team->getFUllname();

                    @endphp
                    <li class="list-item">{{ $name }}

                    </li>
                @endforeach
            </ol>
        </div>
    @endforeach
</div>
