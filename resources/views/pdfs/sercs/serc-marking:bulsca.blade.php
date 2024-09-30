<!DOCTYPE html>
<html lang="en" class="bg-gray-100 w-screen h-screen flex items-center justify-center">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <title>SERC Marking Pack</title>

    <style>
        @page {
            size: A4;
            margin: 0;
        }

        @media print {

            html,
            body {
                width: 210mm;
                height: 297mm;
            }

        }
    </style>

    @if ($brand)
        <style>
            :root {
                --brand-primary: {{ $brand->primary_color }};
                --brand-secondary: {{ $brand->secondary_color }};
            }
        </style>
    @endif
</head>



<body class="w-screen h-screen  flex flex-col space-y-12 print:space-y-0 items-center overflow-x-hidden ">


    <div class="min-h-[297mm] min-w-[210mm] bg-white p-5 flex flex-col  items-center justify-center text-center">
        <h1>SERC Marking Pack</h1>
        <h3>{{ $comp->name }}</h3>
        <p>{{ $location }}<br>{{ $comp->when->format('jS F Y') }}</p>
        <br>
        <br>
        <ol class="list ">
            @foreach ($events as $event)
                <li>{{ count($tanks) * $event->getJudges->count() }}x {{ $event->getName() }} Marking Sheets (Over
                    {{ count($tanks) }} teams)</li>
            @endforeach
        </ol>
        <br>
        <p class="  "><span class="font-semibold">Pages with a black strip at the top mark the begining of a new
                team!</span><br><span class=" print:hidden">(Ensure 'Background graphics' is ticked when
                printing)</span></p>
    </div>


    @foreach ($events as $event)
        @foreach ($tanks->sortBy('serc_order') as $draw => $competitior)
            @foreach ($event->getJudges as $judge)
                <div
                    class="min-h-[297mm] min-w-[210mm] max-w-[210mm] bg-white p-5 flex flex-col grow-0 relative   print:mt-2">
                    @if ($loop->index == 0)
                        <div class="absolute top-0 left-0 w-full h-2 bg-black">&nbsp;</div>
                    @endif

                    <div class="flex w-full justify-between items-center">
                        <h2 class="hmb-0">{{ $event->getName() }}</h2>
                        <p class=" font-semibold text-right">{{ $comp->name }} -
                            {{ $comp->when->format('jS F') }}<br><small>{{ $location }}</small></p>
                    </div>

                    <div class="flex w-full justify-between items-center">
                        <p>Draw {{ $draw + 1 }}</p>
                        <p class=" font-semibold text-right">{{ $judge->name }}</p>
                    </div>


                    <br>
                    <div class="flex justify-between items-center">
                        <h4>{{ $competitior->club }} {{ $competitior->team }}</h4>
                        <p class="text-small text-gray-500">
                        </p>
                    </div>

                    @if ($event->image)
                        <div class="flex items-center justify-center">
                            <img src="{{ asset('storage/' . $event->image) }}" alt="SERC Image" class=" w-[70%] ">

                        </div>
                    @endif


                    @if (strip_tags($judge->description) != '')
                        <p class="font-bold">Marking Hints/Specification</p>
                        <article
                            class="block prose prose-sm prose-neutral prose-p:mb-0 prose-ul:my-0 prose-ol:my-0 prose-li:my-0 !leading-5">
                            {!! $judge->description !!}
                        </article>
                        <div class="mt-3"></div>
                    @endif
                    <table class=" table-fixed text-left ">
                        <thead class="border-b border-black">

                            <tr>
                                <th class="w-full">Criteria</th>
                                <th class="  min-w-[5.5rem] text-right">Mark (/10)</th>


                            </tr>
                        </thead>
                        <tbody class="">


                            @foreach ($judge->getMarkingPoints as $mp)
                                <tr class="border-b border-black  ">
                                    <td class="py-1 border-r border-black indent-3 ">{{ $mp->name }}</td>
                                    <td></td>

                                </tr>
                            @endforeach



                        </tbody>

                    </table>

                    <p><strong>Comments:</strong></p>

                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>

                    <div>
                        <p><strong>Time:</strong>
                            <br>
                            <strong>Signature:</strong>
                        </p>
                    </div>

                    <div class="mt-auto">
                        @php
                            preg_match_all('/\b\w/', $event->getName(), $matches);
                            $firstLetters = implode('', $matches[0]);

                        @endphp
                        <p class="text-sm text-right">IJ -
                            {{ $firstLetters }}
                            -
                            D{{ $draw + 1 }}</p>
                        {{-- ROLE - EVENT - DRAW --}}
                    </div>

                </div>
            @endforeach
        @endforeach
    @endforeach


    <script>
        window.onload = function() {
            window.print()
        }
    </script>

</body>

</html>
