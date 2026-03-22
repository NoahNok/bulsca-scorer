@extends('layouts.competition')

@section('title')
    Edit SERC Draw | Heats and Draws | {{ $comp->name }}
@endsection



@section('content')
    <div class="">
        <div class="flex flex-col space-y-4">

            <div class="flex justify-between">
                <h2 class="mb-0">SERC Order</h2>
                <a href="{{ route('comps.heats_and_draws', $comp) }}" class="se-btn">Back</a>
            </div>

            <p>To swap teams, click the first team, it will turn blue. Then click the team you want to swap it with. The
                page will automatically update and save.</p>




            <div x-data="{
                tanks: {{ $serc->getTankDraw() }},
            
                open_tank: 0,
            
                swap: null,
            
                async onSwap(draw_id) {
            
                    if (this.swap == draw_id) {
                        this.swap = null
                        return
                    }
            
                    if (this.swap == null) {
                        this.swap = draw_id
                        return
                    }
            
                    // Swap is not draw_id and is not null, so submit the swap
            
                    console.log('Swapping', this.swap, draw_id)
            
                    let response = await fetch('{{ route('comps.heats_and_draws.draws.swap', [$comp, $serc]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            swap_from: this.swap,
                            swap_to: draw_id,
                            _token: '{{ csrf_token() }}'
                        })
                    })
            
                    if (response.ok) {
                        let data = await response.json()
                        this.tanks = data.tanks
                        this.swap = null
                        showSuccess('Teams swapped successfully')
                    } else {
                        showAlert('Error swapping teams')
                    }
            
            
            
            
                },
            
                init() {
                    this.open_tank = Math.min(...Object.keys(this.tanks).map(Number))
                }
            }">


                <div class="tabbed-bar mb-4">
                    <template x-for="(_, tank_no) in tanks">
                        <div @click="open_tank = tank_no" :class="open_tank == tank_no ? 'active' : ''">Tank <span
                                x-text="tank_no"></span></div>
                    </template>
                </div>

                <div class="grid grid-flow-row grid-cols-6 gap-4 flex-wrap">

                    <template x-for="draw in tanks[open_tank]">
                        <div class="se-card  se-card-body se-card-hover  p-2! px-4! rounded " @click="onSwap(draw.id)"
                            :class="swap == draw.id ? 'se-card-active' : ''">
                            <span><span x-text="draw.draw"></span>. <span x-text="draw.entity_name"></span></span>

                        </div>
                    </template>

                </div>
            </div>

            <br>

        </div>

        @if (!$comp->getScoringSettings->use_tanks)
            <h4>Regen SERC Order</h4>
            <p>Regenerating the SERC order will randomly assign teams. <strong>You will loose</strong> any alterations you
                have
                made!</p>
            <br>
            <form action="{{ route('comps.heats_and_draws.draws.reset', [$comp, $serc]) }}" method="get"
                @submit="doConfirm($event, 'Are you sure you want to reset the draw?')">
                <button class="se-btn se-btn-danger">Regen</button>
            </form>
        @endif
    </div>
@endsection
