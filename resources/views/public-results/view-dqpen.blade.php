<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="{{ asset('blogo.png') }}" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">

</head>

<body class="overflow-x-hidden">


    <div class="mb-5 relative card" ">

        <div  class="flex justify-between items-center">
            <h3>{{ $dq->getHeat?->getTeam?->getFullname() ?? '-' }}</h3>
            


        </div>



        <div >


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
</body>

</html>
