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
        
            data: {
                'marking_points': [],
                'results': []
            },
        
        
        
            async loadJudge(judge_id) {
                this.data = {
                    'marking_points': [],
                    'results': []
                }
                this.selected = judge_id;
        
                const response = await fetch(this.url.replace('JUDGE_ID', judge_id));
                const data = await response.json();
                console.log(data);
        
                this.data = data;
            }
        }">
            <div class="flex flex-row justify-between items-center mb-2">
                <h2 class="mb-0">Mark Splits - {{ $serc->name }}</h2>

                <div class="sticky top-4 flex space-x-2 ">
                    <a href="{{ route('comps.events.sercs.view', [$comp, $serc]) }}" class="se-btn">Back</a>

                </div>
            </div>


            <div class="flex  space-x-3">
                <template x-for="judge in judges" :key="judge.id">
                    <div class="se-btn" :class="selected == judge.id ? 'se-btn-primary' : ''" @click="loadJudge(judge.id)"
                        x-text="judge.name"></div>
                </template>


            </div>

            <div class="se-table" x-show="data != null && data.marking_points.length > 0" x-transition>
                <table>
                    <thead>
                        <tr>
                            <th>Entry</th>
                            <template x-for="marking_point in data.marking_points" :key="marking_point.id">
                                <th x-text="marking_point.name"></th>
                            </template>
                        </tr>

                    </thead>
                    <tbody>
                        <template x-for="entry in data.results" :key="entry.entity">
                            <tr>
                                <th x-text="entry.entity"></th>
                                <template x-for="marking_point in data.marking_points" :key="marking_point.id">
                                    <td x-data="{
                                        result: entry.results[marking_point.id] ?? null
                                    }" x-text="result?.result ?? '-'"></td>
                                </template>
                            </tr>
                    </tbody>
                </table>
            </div>

        </div>


    </div>
@endsection
