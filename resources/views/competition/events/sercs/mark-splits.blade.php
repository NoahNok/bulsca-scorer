@extends('layouts.competition')

@section('title')
    Mark Splits | {{ $serc->name }}
@endsection



@section('content')
    <div class="grid-3">
        <div class="flex flex-col space-y-4 col-span-3" x-data="{
            judges: {{ $serc->getJudges->map(fn($j) => ['id' => $j->id, 'name' => $j->name])->toJson() }},
            selected: -1,
            url: '{{ route('comps.events.sercs.mark-splits.judge', [$comp, $serc, 'JUDGE_ID']) }}',
        
            judge_data: {
                'mpn': []
            },
        
        
        
            async loadJudge(judge_id) {
                this.selected = judge_id;
        
                const response = await fetch(this.url.replace('JUDGE_ID', judge_id));
                const data = await response.json();
                console.log(data);
        
                this.judge_data = data;
            }
        }">
            <h2>Mark Splits</h2>


            <div class="flex  space-x-3">
                <template x-for="judge in judges" :key="judge.id">
                    <div class="se-btn" :class="selected == judge.id ? 'se-btn-primary' : ''" @click="loadJudge(judge.id)"
                        x-text="judge.name"></div>
                </template>


            </div>

            <div class="se-table">
                <table>
                    <thead>
                        <tr>
                            <template x-for="marking_point_name in judge_data['mpn']" :key="marking_point_name">
                                <th x-text="marking_point_name"></th>
                            </template>
                        </tr>

                    </thead>
                    <tbody>
                        <template x-for="entry_data, entry_name in judge_data['results']">
                            <tr>
                                <th x-text="entry_name"></th>
                                <template x-for="mp in entry_data">
                                    <td x-text="mp.result"></td>
                                </template>
                            </tr>
                    </tbody>
                </table>
            </div>

        </div>


    </div>
@endsection
