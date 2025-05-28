<div class="grid-4">
    @foreach ($comp->getSercTanks()->groupBy('serc_tank')->sortKeys() as $ind => $tank)
        <div class="">
            <h3>Tank {{ $ind }}</h3>
            <ol class=" list-decimal list-inside">
                @foreach ($tank->sortBy('serc_order') as $competitor)
                    @php
                        $team = App\Models\Competitor::find($competitor->tid);
                        $name = $team->getFUllname();

                    @endphp
                    <li class="list-item whitespace-nowrap overflow-ellipsis overflow-hidden hover:whitespace-normal">
                        {{ $name }}</li>
                @endforeach
            </ol>
        </div>
    @endforeach
</div>
