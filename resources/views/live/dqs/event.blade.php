@extends('live.layout')

@section('content')
    <div class="w-[90vw] md:w-[70vw] my-12">
        <div class="w-full flex items-center justify-between">
            <h1 class="mb-0">{{ $comp->name }}</h1>
            <h2 id="time-now"></h2>
        </div>
        <br>
        <h3 class="-mb-1"> {{ $event->getName() }} - DQs & Penalties</h3>
        <a href="{{ route('live.dqs', request()->all()) }}" class="link "><span class="text-sm">Back</span></a>
        <br>
        <br>
        @forelse ($dqs as $dq)
            <div class="mb-5 relative card" x-data="{ collapsed: true }">

                <div @click="collapsed = !collapsed" class="flex justify-between items-center">
                    <div class="flex space-x-5">
                        <h3>{{ $dq->getHeat?->getTeam?->getFullname() ?? '-' }}
                        </h3>
                        @if ($dq->appealed)
                            <div class=" flex items-center justify-center ">
                                <dib class="btn btn-thin btn-info">Appealed</dib>
                            </div>
                        @endif
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        :class="!collapsed ? '' : 'rotate-180'" stroke="currentColor" class="w-6 h-6 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>


                </div>



                <div x-show="!collapsed" x-collapse style="display: none">


                    <div class="flex justify-between">
                        <p><strong>Heat</strong>: {{ $dq->getHeat?->heat ?? '-' }}
                            <strong>Lane</strong>: {{ $dq->getHeat?->lane ?? '-' }} </span>
                        </p>
                    </div>

                    <div class="flex justify-between">
                        <p><strong>Team</strong>: {{ $dq->getHeat?->getTeam?->getFullname() ?? '-' }}</p>
                        <p><strong>Turn</strong>: {{ $dq['turn'] ?? '-' }} <strong>Length</strong>:
                            {{ $dq['length'] ?? '-' }}
                        </p>
                    </div>


                    <br>


                    <div class="flex space-x-4">
                        <p><strong>Reporter</strong>: {{ $dq['name'] ?? '-' }} ({{ $dq['position'] ?? '-' }})
                        </p>
                        <p><strong>Seconder</strong>: {{ $dq['seconder_name'] ?? '-' }}
                            ({{ $dq['seconder_position'] ?? '-' }})
                        </p>
                    </div>



                    <br>



                    <h4 class="text-bulsca_red">{{ $dq['code'] ?? '-' }}</h4>



                    <div name="" id="" readonly class="w-full  -mt-2 text-sm text-gray-500 mb-6">
                        @php
                            if (str_starts_with($dq['code'], 'P')) {
                                $code = substr($dq['code'], 1);

                                $code = App\Models\PenaltyCode::find($code)->description ?? 'Penalty code not found';
                            } else {
                                $code = substr($dq['code'], 2);

                                $code = App\Models\DQCode::find($code)->description ?? 'DQ code not found';
                            }
                        @endphp
                        {{ $code }}
                    </div>

                    <p class="font-semibold">Additional Judge Details</p>
                    <p class="">{{ $dq['details'] ?? '-' }}</p>





                </div>


            </div>
        @empty
            <p>None submitted 🎉 </p>
        @endforelse





        </body>
    @endsection
