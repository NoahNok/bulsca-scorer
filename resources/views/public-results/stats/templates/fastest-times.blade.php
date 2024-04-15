<div class="grid grid-cols-6 gap-x-5 w-full ">
    @foreach ($data as $record)
    <div class="card " x-speed-event="{{ $record->event }}" x-speed-record="{{ $record->time }}">

        <div class="flex ">
            <p class="font-semibold  ">{{ $record->event }}</p>
            <a class="link ml-auto" href="{{ route('public.results.comp', ($record->competition ?? '') . '.' . ($record->competition_id ?? '')) }}"><span class="text-sm">{{ $record->competition ?? '-' }}</span></a>
        </div>
        <h3 class="hmb-0">
            {{ $record->time == 99999999999999999 ? '-' : App\Models\SpeedResult::getPrettyTime($record->time) }}
        </h3>
        <p class=" text-sm flex">
            <span>{{ round($record->points) }}pts</span>
            <span class="ml-auto">{{ $record->club }} {{ $record->team }}  </span>
           
        </p>
        

    </div>
@endforeach
</div>