<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Editor | WhatIf | BULSCA</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
</head>

<body>
    <div class="w-screen h-screen p-3" x-data="start()">
        <div class="w-full flex space-x-3 py-2 px-4  items-center">
            <h1 class=" text-[2rem] text-transparent bg-clip-text bg-gradient-to-r from-bulsca via-purple-500 to-bulsca_red"
                style="margin-bottom: 0 !important">
                WhatIf</h1>
            <div class="flex-grow"></div>
            <h1 class="text-[1.5rem]" style="margin-bottom: 0 !important">{{ $comp->name }}</h1>
        </div>
        <div class="w-full flex space-x-3 px-2  border rounded-full bg-gray-100 items-center">
            <div class="py-1 px-3 ">Events</div>

            <div class="pill-select">
                @foreach ($comp->getSERCs as $serc)
                    <div class=" pill-select-option" @click="switchEvent('se:{{ $serc->id }}')"
                        :class="pillActive('se:{{ $serc->id }}')">
                        {{ $serc->getName() }}</div>
                @endforeach
                @foreach ($comp->getSpeedEvents as $speed)
                    <div class=" pill-select-option" @click="switchEvent('sp:{{ $speed->id }}')"
                        :class="pillActive('sp:{{ $speed->id }}')">
                        {{ $speed->getName() }}</div>
                @endforeach
            </div>

        </div>

        <div class="w-full p-5 flex">
            <p style="display: none" class="w-[70%]" x-show="currentEvent == null">No event selected</p>

            <div style="display: none" class="w-[70%]" x-show="currentEvent != null">

                @foreach ($comp->getSERCs as $serc)
                    <div style="display: none" x-show="currentEvent == 'se:{{ $serc->id }}'">

                        <div x-data="{
                            sdata: {{ json_encode($serc->getDataAsJson()) }},
                        
                            onlyMps() {
                                let mps = [];
                                this.sdata.judges.forEach(j => {
                                    mps.push(...j.marking_points)
                                })
                                return mps;
                        
                        
                            },
                        
                            onChange(newValue, srId) {
                                console.log(newValue, srId)
                        
                        
                                let fd = new FormData();
                                fd.append('_token', '{{ csrf_token() }}')
                                fd.append('id', srId)
                                fd.append('result', newValue)
                        
                                fetch('{{ route('whatif.userc') }}', {
                                    method: 'POST',
                                    body: fd
                                })
                            },
                        }">
                            <h2>{{ $serc->getName() }}</h2>


                            <table class="table">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="p-2 text-center border-r">Team</th>
                                        <template x-for="judge in sdata.judges" :key="judge.id">
                                            <th class="p-2 text-center border-r" :colspan="judge.marking_points.length"
                                                x-text="judge.name">
                                            </th>
                                        </template>
                                    </tr>
                                    <tr>
                                        <th class="border-r"></th>
                                        <template x-for="mp in (onlyMps())" :key="mp.id">
                                            <th class="px-2 border-r" x-text=" mp.name"></th>
                                        </template>


                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="team in sdata.teams" :key="team.id">
                                        <tr>
                                            <td class="border-r p-2" x-text="team.name"></td>
                                            <template x-for="mp in (onlyMps())" :key="mp.id">
                                                <td class=" border-r">
                                                    <input class=" w-auto"
                                                        x-on:change.debounce="onChange($event.target.value, sdata.data[mp.id][team.id].id )"
                                                        x-model=" sdata.data[mp.id][team.id].result" />
                                                </td>

                                            </template>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>


                            @php dump($serc->getDataAsJson()) @endphp
                        </div>

                    </div>
                @endforeach


            </div>

            <div class="w-[30%]">
                <h1>Results</h1>
                {{ $comp->getResultSchemas->first()->getEvents }}
                <iframe src="{{ route('whatif.editor.results', $comp->getResultSchemas->first()) }}"
                    frameborder="0"></iframe>
            </div>


        </div>
    </div>

    <script>
        function start() {
            return {
                currentEvent: null,

                switchEvent(event) {
                    this.currentEvent = event
                },

                pillActive(name) {
                    return this.currentEvent == name ? 'selected' : ''
                }
            }
        }
    </script>
</body>

</html>
