<div class="card " x-data="{ show: 1 }">

    <div class="flex space-x-2 items-center justify-between">
        <h3>{{ $data['league']  }}</h3>
        <div class="flex space-x-2">
            <div class=" transition-colors rounded-full py-1 px-4 text-xs bg-gray-200 cursor-pointer hover:bg-bulsca hover:text-white"
                :class="show == 1 && 'bg-bulsca text-white'" @click="show=1">Graph</div>
            <div class="  transition-colors rounded-full py-1 px-4 text-xs bg-gray-200 cursor-pointer hover:bg-bulsca hover:text-white"
                :class="show == 2 && 'bg-bulsca text-white'" @click="show=2">Table</div>
        </div>
    </div>
    <canvas id="placingChart-{{ $data['team'] }}-{{ $data['league']  }}" x-league="{{ $data['league']  }}" x-show="show==1"></canvas>




    <div class="w-full h-full overflow-x-auto" x-show="show==2" style="display:none">
        <table class="table-auto ">
            <thead class="text-left">
                <tr>

                    @foreach ($data['competedAt'] as $comp)
                        <th class="px-2 whitespace-nowrap ">{{ $comp->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @php
                    $nf = new NumberFormatter('en-GB', NumberFormatter::ORDINAL);

                @endphp


                <tr>


                    @foreach ($data['competedAt'] as $comp)
                        <td class="px-2 ">
                            {{ $data['placings'][0][$comp->id] ?? false ? $nf->format($data['placings'][0][$comp->id]['place']) : '-' }}
                        </td>
                    @endforeach
                </tr>



            </tbody>
        </table>
    </div>


</div>
<script>
    var ctx = document.getElementById('placingChart-{{ $data['team'] }}-{{ $data['league'] }}').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: [
                @foreach ($data['competedAt'] as $comp)
                    '{{ $comp->name }}',
                @endforeach
            ],
            datasets: [

                {
                    label: '{{ $data['league'] }}',
                    data: [
                        @foreach ($data['competedAt'] as $comp)
                            {{ $data['placings'][0][$comp->id] ?? false ? $data['placings'][0][$comp->id]['place'] : 'null' }},
                        @endforeach
                    ],

                    fill: false,
                    tension: 0.1,
                    spanGaps: true,
                },

            ]
        },
        options: {
            scales: {
                y: {

                    reverse: true,

                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        callback: function(value, index, values) {
                            return addSuffix(value);
                        },

                    }
                }
            }
        }
    });
</script>