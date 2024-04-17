<div class=" flex flex-row  md:grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 4xl:grid-cols-6 gap-x-5 w-full gap-y-2  snap-x snap-mandatory overflow-x-auto w-full">


    @foreach (App\Models\SpeedEvent::orderBy('name')->get() as $event)

        @php
            $record = null;
            foreach ($data as $r) {
                if ($r->event == $event->id) {
                    $record = $r;
                    break;
                }
            }
        @endphp

        <div class="card  min-w-full  snap-center " >

            <div class="flex ">
                <p class="font-semibold  ">{{ $event->name }}</p>
                <a class="link ml-auto" href="{{ route('public.results.comp', ($record->competition ?? '') . '.' . ($record->competition_id ?? '')) }}"><span class="text-sm">{{ $record->competition ?? '-' }}</span></a>
            </div>
            <h3 class="hmb-0">
                @if ($record)
                    {{  $record->time == 99999999999999999 ? '-' : App\Models\SpeedResult::getPrettyTime($record->time) }}
                @else
                    -    
                @endif
                
            </h3>
            <p class=" text-sm flex">
                <span>@if ($record) {{ round($record->points) }} pts @else No data @endif</span>
                <span class="ml-auto">@if($record){{ $record->club }} {{ $record->team }}@endif  </span>
            
            </p>
            

        </div>
    @endforeach


</div>
<div class="md:hidden"><small>Swipe for more</small></div>