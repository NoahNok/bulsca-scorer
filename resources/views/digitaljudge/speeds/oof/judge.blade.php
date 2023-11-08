@extends('digitaljudge.mpa-layout')

@section('title')
    {{ $speed->getName() }}
@endsection
@php
    $backlink = route('dj.speeds.oof.index', $speed);
    $icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />';
@endphp

@section('content')
    <div class="flex flex-col space-y-3 w-full ">

        <h2 class="font-bold  w-full break-words">
            Heat {{ $heat }}
        </h2>

        <p>Click lanes in the order they finish. Thye will turn green once clicked and a place will appear in the box to
            the left. You can ignore empty white lanes.</p>


        @php
            $existingLanes = $comp
                ->getHeatEntries()
                ->where('heat', $heat)
                ->orderBy('lane')
                ->get();

            $lanes = [];
            $maxLanes = 0;
            $hasAssigned = false;

            for ($lane = 1; $lane <= $comp->getMaxLanes(); $lane++) {
                if ($existingLanes->contains('lane', $lane)) {
                    $pLane = $existingLanes->where('lane', $lane)->first();

                    $sr = App\Models\SpeedResult::where('competition_team', $pLane->team)
                        ->where('event', $speed->id)
                        ->first();

                    $mins = floor($sr->result / 60000);
                    $secs = ($sr->result - $mins * 60000) / 1000;

                    $lanes[$lane] = ['number' => $lane, 'name' => $pLane->getTeam->getFullname(), 'id' => $pLane->team];

                    if ($pLane->getOOF($speed->id) != null) {
                        $lanes[$lane]['place'] = $pLane->getOOF($speed->id)->oof;
                        $hasAssigned = true;
                    }

                    $maxLanes++;
                } else {
                    $lanes[$lane] = ['number' => $lane, 'name' => 'Empty', 'id' => null];
                }
            }

            $targetUrl = route('dj.speeds.oof.judge', [$speed, $heat + 1]);
            if ($heat + 1 > DigitalJudge::getClientCompetition()->getMaxHeats()) {
                $targetUrl = route('dj.speeds.oof.index', $speed);
            }

        @endphp



        <div class="flex flex-col space-y-3" x-data="{
            lanes: {{ json_encode($lanes) }},
        
            place: 1,
            maxAssignable: {{ $maxLanes }},
            canReassign: {{ $hasAssigned ? 1 : 0 }},
            checked: false,
        
            clickOrder(lane) {
                if (lane.id == null || lane.place != null) return
                this.lanes[lane.number].place = this.place
        
        
                if (this.place == this.maxAssignable) {
                    this.canReassign = true
                }
                this.place++
        
            },
        
        
        
            startReassign() {
                if (!confirm('Are you sure you want to re-assign?')) return
        
                this.place = 1;
        
                for (lane in this.lanes) {
                    this.lanes[lane].place = null
                }
                this.canReassign = false
            },
        
            save() {
        
                if (this.canReassign == false) return alert('Please order all available lanes first')
        
                if (!this.checked) return alert('Please check the box to confirm the results are correct')
        
                let data = []
        
                for (lane in this.lanes) {
                    if (this.lanes[lane].id == null) continue
        
                    data.push({
                        lane: this.lanes[lane].number,
                        place: this.lanes[lane].place,
                        team: this.lanes[lane].id
                    })
                }
        
                fetch('{{ route('dj.speeds.oof.judgePost', [$speed, $heat]) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            showSuccess('Saved!')
                            setTimeout(() =>
                                location.href = `{{ $targetUrl }}`, 500)
                        } else {
                            alert('Error!')
                        }
                    })
            }
        
        
        
        }">


            <template x-for="lane in lanes">

                <div class="flex space-x-2">
                    <div class="btn btn-white">
                        <span x-text="lane.place == null ? '-' : lane.place"></span>
                    </div>
                    <button class="btn w-full" @click="clickOrder(lane)"
                        :class="lane.id ? (lane.place ? 'btn-success' : 'btn-primary') : 'btn-white'">
                        Lane <span x-text="lane.number"></span>: <span x-text="lane.name"></span>
                    </button>
                </div>


            </template>


            <button class="btn btn-danger" x-show="canReassign" @click="startReassign()">Re-assign</button>

            <br>

            <div class="flex flex-row space-x-2 md:space-x-4 items-center">

                <label for="confirm">I acknowledge that the above results are correct and cannot be changed, and
                    submission of this form acts as signing it digitally.
                    <br>
                    <small class="text-gray-500">(Clicking the text will also check the box!)</small>
                </label>
                <input type="checkbox" required x-model="checked" name="" class="min-w-[20px] min-h-[20px]"
                    id="confirm">
            </div>

            <button @click="save()" class="btn w-full "
                :class="canReassign == true ? 'btn-success' : 'btn-white cursor-not-allowed'">Save &
                Next</button>

        </div>














    </div>
@endsection
