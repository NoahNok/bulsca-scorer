@extends('digitaljudge.layout')

@section('content')
    <div x-data="{
        open: false,
        loading: false,
    
        judges: [],
    
        loadMarks() {
            this.open = true
            this.loading = true;
            this.judges = [];
            fetch('{{ route('dj.judging.previous-marks') }}').then(res => res.json()).then(data => {
                console.log(data);
    
                this.loading = false;
                this.judges = data;
    
    
    
    
            })
    
        },
    }" class=" w-screen flex flex-col  mt-8 space-y-4">
        <div class="flex w-full items-center justify-center space-x-4">
            <img src="{{ asset('blogo.png') }}" alt="BULSCA Logo" class=" w-32 h-32 ">
            <div class="flex flex-col">
                <h5 class="-mb-1 text-sm">DigitalJudge</h5>
                <h3 class="-mb-1">{{ $serc->name }}</h3>
                <h5 class="text-sm">{{ $comp->name }} </h5>
            </div>
        </div>


        <div class="px-4 pb-4 space-y-2">
            <a href="{{ route('dj.judging.home') }}" class="link">Home</a>
            <p>You are <strong class="text-bulsca">
                    @forelse ($judges as $judge)
                        {{ $judge->name }}
                        @if (!$loop->last)
                            {{ ',' }}
                        @endif
                    @empty
                    @endforelse
                </strong></p>
            <br>
            <p class="text-xl">Team: <strong class="text-bulsca">{{ $team->getFullname() }}</strong></p>


            <hr>

            <form action="" method="post">
                <div class="flex flex-col space-y-6 ">
                    @foreach ($judges as $mJudge)
                        <div>
                            <h4>{{ $mJudge->name }} </h4>
                            <p x-on:click="loadMarks()" class="-mt-2 text-sm text-blue-700 hover:underline cursor-pointer">
                                Previous Marks</p>
                        </div>


                        @foreach ($mJudge->getMarkingPoints as $mp)
                            @php
                                $mpValue = $head ? $mp->getScoreForTeam($team->id) : -1;
                            @endphp
                            <div class="flex flex-col space-y-2 border-b pb-4">
                                <div class="flex justify-between items-center ">
                                    <p>{{ $mp->name }}</p>
                                    <div class="flex items-center justify-center">
                                        <input type="radio" required class="w-0 h-0 peer" value="0"
                                            name="mp-{{ $mp->id }}" @if ($mpValue == 0) checked @endif
                                            id="mp-{{ $mp->id }}-0">
                                        <label for="mp-{{ $mp->id }}-0"
                                            class="  flex items-center justify-center px-4 py-0.5 font-semibold  rounded-sm bg-gray-200 text-xs peer-checked:bg-bulsca_red peer-checked:text-white ">
                                            ZERO
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-5 gap-2 gap-y-4">
                                    @for ($i = 1; $i <= 10; $i++)
                                        <div class="flex items-center justify-center">
                                            <input type="radio" required class="w-0 h-0 peer" value="{{ $i }}"
                                                name="mp-{{ $mp->id }}"
                                                @if ($mpValue == $i) checked @endif
                                                id="mp-{{ $mp->id }}-{{ $i }}">
                                            <label for="mp-{{ $mp->id }}-{{ $i }}"
                                                class="w-6 h-6 flex items-center justify-center p-4 font-semibold font-mono rounded-md bg-gray-200 text-sm peer-checked:bg-bulsca peer-checked:text-white ">
                                                {{ $i }}
                                            </label>
                                        </div>
                                    @endfor
                                </div>
                                <div class="text-gray-500 pt-2 flex justify-between">
                                    <small>Min:
                                        {{ round(App\Models\SERCResult::where('marking_point', $mp->id)->min('result')) ?: '0' }}</small><small>Avg:
                                        {{ round(App\Models\SERCResult::where('marking_point', $mp->id)->avg('result'), 1) ?: '0' }}</small><small>Max:
                                        {{ round(App\Models\SERCResult::where('marking_point', $mp->id)->max('result')) ?: '0' }}</small>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
                <br>

                <div>
                    <h4>Notes</h4>
                    @php
                        $n = '';
                    @endphp
                    @if ($head)
                        @php
                            $n = App\Models\DigitalJudge\JudgeNote::where('team', $team->id)
                                ->where('judge', $judges[0]->id)
                                ->first();
                        @endphp
                    @endif
                    <textarea @if ($head && DigitalJudge::hasTeamBeenJudgedAlready($team)) disabled @endif name="team-notes" rows="5"
                        placeholder="Type your notes for this team here..."
                        class="w-full border hover:border-gray-400 p-3 h-max focus:border-gray-400 outline-none rounded-md" id="">{{ $n ? $n->note : '' }}</textarea>
                </div>
                <br>
                <div class="flex flex-row space-x-2 md:space-x-4 items-center">

                    <label for="confirm">I acknowledge that the above results are correct and cannot be changed, and
                        submission of this form acts as signing it digitally.
                        <br>
                        <small class="text-gray-500">(Clicking the text will also check the box!)</small>
                    </label>
                    <input type="checkbox" required name="" class="min-w-[20px] min-h-[20px]" id="confirm">
                </div>
                <br>
                @csrf
                <button type="submit" class="btn w-full">Submit</button>
            </form>

        </div>


        <div x-show="open" class="judge-notes fixed top-0 left-0 w-screen  h-screen overflow-scroll bg-white py-2 z-10  "
            style="display: none; margin-top: 0 !important">
            <div class="flex flex-col  mx-4 ">
                <h1 class="text-center">Previous Marks</h1>
                <div class="flex items-center justify-center">
                    <p class="link" x-on:click="open = !open">Close</p>
                </div>

                <div x-show="loading"><br><x-loader /></div>

                <div class="flex flex-col items-start ">
                    <template x-for="judge in judges">
                        <div class="border-b pb-4 mb-3 last-of-type:border-b-0 border-b-gray-300 max-w-full ">
                            <h2 class=" whitespace-nowrap text-ellipsis overflow-hidden hover:whitespace-normal focus:whitespace-normal"
                                x-text="judge.name"></h2>




                            <div class="  relative overflow-x-auto   ">
                                <table
                                    class=" text-sm w-full shadow-md rounded-lg top-0 text-left text-gray-500 border-collapse  relative">
                                    <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                                        <tr>
                                            <th class="py-3 px-3  sticky left-0 top-0 bg-gray-50">Marking
                                                Point</th>
                                            <template x-for="mark in judge.mp[0].marks">

                                                <th x-text="mark.team" class="py-3 px-3 whitespace-nowrap"
                                                    style="writing-mode: vertical-rl"></th>


                                            </template>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="mp in judge.mp">
                                            <tr class="bg-white border-b text-right ">
                                                <td class="py-2 px-3 sticky left-0 top-0 bg-white text-ellipsis whitespace-nowrap overflow-hidden max-w-[1px] hover:max-w-none focus:max-w-none "
                                                    x-text="mp.name" style=""></td>
                                                <template x-for="mark in mp.marks">


                                                    <td class="py-2 px-3"
                                                        x-text="mark.mark == null ? '-' : Math.round(mark.mark)"></td>

                                                </template>
                                            </tr>
                                        </template>
                                    </tbody>

                                </table>
                            </div>








                        </div>
                    </template>

                </div>
                <br>
                <div class="flex items-center justify-center">
                    <p class="link" x-on:click="open = !open">Close</p>
                </div>
            </div>
        </div>


    </div>







    </div>

    <div class="fixed top-0 right-0 border-b border-l rounded-bl-md p-1 pb-2 px-4 text-md border-gray-300 bg-bulsca text-white font-semibold"
        id="notes-open">
        Notes
    </div>

    <div class="hidden judge-notes fixed top-0 left-0 w-full  h-full overflow-scroll bg-white  p-4" id="notes-pane">
        <div class="flex flex-col items-center ">
            <h1>Your Notes</h1>
            <p class="link" id="notes-close-1">Close</p>

            <div class="flex flex-col items-start ">
                @foreach ($judges[0]->getNotes as $note)
                    <div class="border-b pb-4 mb-3 last-of-type:border-b-0 border-b-gray-300">
                        <h3>{{ $note->getTeam->getFullname() }}</h3>
                        <p>{{ $note->note }}</p>
                    </div>
                @endforeach
            </div>
            <br>
            <p class="link" id="notes-close-2">Close</p>
        </div>
    </div>



    <script>
        const np = document.getElementById("notes-pane");

        const toggle = (e) => {
            np.classList.toggle("hidden")
        }

        document.getElementById('notes-close-1').onclick = toggle;
        document.getElementById('notes-close-2').onclick = toggle;
        document.getElementById('notes-open').onclick = toggle;
    </script>
@endsection
