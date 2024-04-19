@php

    $bestPoints;
    $bestPlace;

    foreach ($data as $row) {
        if (!isset($bestPoints)) {
            $bestPoints = $row;
        }
        if (!isset($bestPlace)) {
            $bestPlace = $row;
        }

        if ($row->points > $bestPoints->points) {
            $bestPoints = $row;
        }

        if ($row->place < $bestPlace->place) {
            $bestPlace = $row;
        }
    }

    $nf = new NumberFormatter('en-GB', NumberFormatter::ORDINAL);

@endphp

@if(!isset($hideBlock))
<div class="card card-bulsca-solid items-center justify-center col-span-full 4xl:col-span-1">
    <img src="https://www.bulsca.co.uk/storage/logo/blogo.png" class="w-40 h-40" alt="">
</div>    
@endif




<div class="col-span-full 3xl:col-span-2  grid grid-rows-3 gap-2 ">

    <div class="card  md:row-span-2 z-20 ">
        <h3 class="hmb-0">Best SERCs</h3>



        <table>

            <thead>
                <tr class="text-left">
                    <th>SERC</th>
                    <th>Score</th>
                    <th>Team</th>
                    <th>Competition</th>
                </tr>
            </thead>

            <tbody>
                @forelse (array_splice($data, 0, 5, true) as $row)
                    <tr>
                        <td>{{ $row->serc_name }}</td>
                        <td>{{ round($row->score) }}/{{ round($row->serc_max) }}
                            (<strong>{{ round(($row->score / $row->serc_max) * 100, 2) }}%</strong>)</td>
                        <td>@if ($stat_target == App\Stats\StatTarget::GLOBAL) {{ $row->club }} @endif  {{ $row->team }}</td>
                        <td><a class="link"
                                href="{{ route('public.results.comp', $row->competition_name . '.' . $row->competition_id) }}">{{ $row->competition_name }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan=4 class="  text-center"><small>No data</small></td>
                    </tr>
                @endforelse
            </tbody>

        </table>
    </div>


    
    <div class="grid md:grid-cols-2  gap-2">
        
        <div class="card  min-w-full  relative justify-center z-10">

            <div class="absolute w-1 h-3 bg-bulsca_red -top-3 "></div>

            <div class="flex ">
                <p class="font-semibold  ">Best Score</p>

                
                @isset ($bestPoints)
                <a class="link ml-auto"
                    href="{{ route('public.results.comp', $bestPoints->competition_name . '.' . $bestPoints->competition_id) }}"><span
                        class="text-sm">{{ $bestPoints->competition_name }}</span></a>
                        @endisset
            </div>
            <h3 class="hmb-0">
                {{ isset($bestPoints) ? round($bestPoints->points) : '-' }} pts
            </h3>
            <p class=" text-sm flex">

                <span>{{ $bestPoints?->club ?? 'No data' }} {{ $bestPoints?->team ?? ''}} </span>

            </p>
        </div>
       

       
        <div class="card  min-w-full  relative justify-center">

            <div class="absolute w-1 h-3 bg-bulsca_red -top-3 "></div>

            <div class="flex ">
                <p class="font-semibold  ">Best Place</p>
                @isset ($bestPoints)
                <a class="link ml-auto"
                    href="{{ route('public.results.comp', $bestPlace->competition_name . '.' . $bestPlace->competition_id) }}"><span
                        class="text-sm">{{ $bestPlace->competition_name }}</span></a>
                        @endisset
            </div>
            <h3 class="hmb-0">
                {{  isset($bestPoints) ? $nf->format(round($bestPlace->place)) : '-' }}
            </h3>
            <p class=" text-sm flex">

                <span>{{ $bestPlace?->club ?? 'No data' }} {{ $bestPlace?->team ?? '' }} </span>

            </p>
        </div>
   

    </div>

</div>
