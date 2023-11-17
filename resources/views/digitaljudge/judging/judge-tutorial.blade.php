@extends('digitaljudge.mpa-layout')

@section('title')
    Tutorial SERC
@endsection
@section('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/minified/introjs.min.css">
@endsection
@php
    $nopad = true;
    $backlink = route('dj.judging.home');
    $icon =
        ' <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 011.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 00-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 010 9.424m-4.138-5.976a3.736 3.736 0 00-.88-1.388 3.737 3.737 0 00-1.388-.88m2.268 2.268a3.765 3.765 0 010 2.528m-2.268-4.796a3.765 3.765 0 00-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 01-1.388.88m2.268-2.268l4.138 3.448m0 0a9.027 9.027 0 01-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0l-3.448-4.138m3.448 4.138a9.014 9.014 0 01-9.424 0m5.976-4.138a3.765 3.765 0 01-2.528 0m0 0a3.736 3.736 0 01-1.388-.88 3.737 3.737 0 01-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 01-1.652-1.306 9.027 9.027 0 01-1.306-1.652m0 0l4.138-3.448M4.33 16.712a9.014 9.014 0 010-9.424m4.138 5.976a3.765 3.765 0 010-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 011.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 00-1.652 1.306A9.025 9.025 0 004.33 7.288" />';

@endphp

@section('content')
    <div x-data="{
        open: false,
        loading: false,
    
        judges: [],
    
        loadMarks() {
            this.open = true
            this.loading = false
    
        },
    }" class=" flex flex-col   space-y-3"
        data-intro="Welcome to the DigitalJudge SERC tutorial. You can end this at any time by clicking the 'X' icon."
        data-title="SERC Tutorial">



        <div class="px-4 pb-4 space-y-2">
            <p data-intro="This is a list of the current objective(s) you're marking.">You are <strong class="text-bulsca">
                    Judge 1: Head Bleed
                </strong></p>
            <br>
            <p class="text-xl" data-intro="This is the current team you are marking.">Team: <strong class="text-bulsca">Team
                    1</strong></p>


            <hr>


            <form action="" method="post">
                <div class="flex flex-col space-y-6 ">

                    <div
                        data-intro="Each objective has a name in blue. <br><br> You can view all your previous marks using the light blue 'Previous Marks' link.">
                        <h4>Judge 1: Head Bleed </h4>
                        <p x-on:click="loadMarks()" class="-mt-2 text-sm text-blue-700 hover:underline cursor-pointer">
                            Previous Marks</p>
                    </div>




                    <div class="flex flex-col space-y-2 border-b pb-4" id="mpcontainer-1"
                        data-intro="Each marking point has a name given in black. <br><br>To give a mark simply select the score between 0-10. If you miss one it will be highlighted when you try to submit the form.">
                        <div class="flex justify-between items-center ">
                            <p>Treatment</p>
                            <div class="flex items-center justify-center">
                                <input type="radio" required class="w-0 h-0 peer" value="0" name="mp-1"
                                    id="mp-1-0">
                                <label for="mp-1-0"
                                    class="  flex items-center justify-center px-4 py-0.5 font-semibold  rounded-sm bg-gray-200 text-xs peer-checked:bg-bulsca_red peer-checked:text-white ">
                                    ZERO
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-2 gap-y-4">
                            @for ($i = 1; $i <= 10; $i++)
                                <div class="flex items-center justify-center">
                                    <input type="radio" required class="w-0 h-0 peer" value="{{ $i }}"
                                        name="mp-1" id="mp-1-{{ $i }}">
                                    <label for="mp-1-{{ $i }}"
                                        class="w-6 h-6 flex items-center justify-center p-4 font-semibold font-mono rounded-md bg-gray-200 text-sm peer-checked:bg-bulsca peer-checked:text-white ">
                                        {{ $i }}
                                    </label>
                                </div>
                            @endfor
                        </div>
                        <div class="text-gray-500 pt-2 flex justify-between"
                            data-intro="You're also shown a min, max and avgerage score at all times for each marking point as a quick reference.">
                            <small>Min:
                                1</small><small>Avg:
                                2</small><small>Max:
                                3</small>
                        </div>
                    </div>

                    <div class="flex flex-col space-y-2 border-b pb-4" id="mpcontainer-2">
                        <div class="flex justify-between items-center ">
                            <p>Aftercare</p>
                            <div class="flex items-center justify-center">
                                <input type="radio" required class="w-0 h-0 peer" value="0" name="mp-2"
                                    id="mp-2-0">
                                <label for="mp-2-0"
                                    class="  flex items-center justify-center px-4 py-0.5 font-semibold  rounded-sm bg-gray-200 text-xs peer-checked:bg-bulsca_red peer-checked:text-white ">
                                    ZERO
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-5 gap-2 gap-y-4">
                            @for ($i = 1; $i <= 10; $i++)
                                <div class="flex items-center justify-center">
                                    <input type="radio" required class="w-0 h-0 peer" value="{{ $i }}"
                                        name="mp-2" id="mp-2-{{ $i }}">
                                    <label for="mp-2-{{ $i }}"
                                        class="w-6 h-6 flex items-center justify-center p-4 font-semibold font-mono rounded-md bg-gray-200 text-sm peer-checked:bg-bulsca peer-checked:text-white ">
                                        {{ $i }}
                                    </label>
                                </div>
                            @endfor
                        </div>
                        <div class="text-gray-500 pt-2 flex justify-between">
                            <small>Min:
                                1</small><small>Avg:
                                2</small><small>Max:
                                3</small>
                        </div>
                    </div>

                </div>
                <br>

                <div
                    data-intro="You can also add notes about each team. You can view previous notes at any time using the 'Blue' notes button at the top right of the screen. <br><br>Please note that teams can see these notes after the competition, but they will be anonymous!">
                    <h4>Notes</h4>


                    <textarea name="team-notes" rows="5" placeholder="Type your notes for this team here..."
                        class="w-full border hover:border-gray-400 p-3 h-max focus:border-gray-400 outline-none rounded-md" id=""></textarea>
                </div>
                <br>
                <div class="flex flex-row space-x-2 md:space-x-4 items-center"
                    data-intro="You'll need to confirm your results before submitting. This acts as a digital signature tracking your marks. All marks you submit are logged and cannot be changed by you.">

                    <label for="confirm">I acknowledge that the above results are correct and cannot be changed, and
                        submission of this form acts as signing it digitally.
                        <br>
                        <small class="text-gray-500">(Clicking the text will also check the box!)</small>
                    </label>
                    <input type="checkbox" required name="" class="min-w-[20px] min-h-[20px]" id="confirm">
                </div>
                <br>
                @csrf
                <button type="submit" onclick="submissionCheck()" class="btn w-full"
                    data-intro="To complete the tutorial, mark this fake team and submit their marks using the button below or click the back arrow in the top left.
                    <br><br> You'll need to close this dialogue first. You can restart the tutorial by refreshing the page or by clicking the purple tutorial button on the previous page!">Submit</button>
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

                    <div class="border-b pb-4 mb-3 last-of-type:border-b-0 border-b-gray-300 max-w-full ">
                        <h2
                            class=" whitespace-nowrap text-ellipsis overflow-hidden hover:whitespace-normal focus:whitespace-normal">
                            Judge 1: Head Bleed</h2>




                        <div class="  relative overflow-x-auto   ">
                            <table
                                class=" text-sm w-full shadow-md rounded-lg top-0 text-left text-gray-500 border-collapse  relative">
                                <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                                    <tr>
                                        <th class="py-3 px-3  sticky left-0 top-0 bg-gray-50">Marking
                                            Point</th>


                                        <th class="py-3 px-3 whitespace-nowrap" style="writing-mode: vertical-rl">Team
                                            1</th>
                                        <th class="py-3 px-3 whitespace-nowrap" style="writing-mode: vertical-rl">Team
                                            2</th>
                                        <th class="py-3 px-3 whitespace-nowrap" style="writing-mode: vertical-rl">Team
                                            3</th>



                                    </tr>
                                </thead>
                                <tbody>

                                    <tr class="bg-white border-b text-right ">
                                        <td class="py-2 px-3 sticky left-0 top-0 bg-white text-ellipsis whitespace-nowrap overflow-hidden max-w-[1px] hover:max-w-none focus:max-w-none "
                                            style="">Treatment</td>



                                        <td class="py-2 px-3">-</td>
                                        <td class="py-2 px-3">-</td>
                                        <td class="py-2 px-3">-</td>

                                    </tr>

                                    <tr class="bg-white border-b text-right ">
                                        <td class="py-2 px-3 sticky left-0 top-0 bg-white text-ellipsis whitespace-nowrap overflow-hidden max-w-[1px] hover:max-w-none focus:max-w-none "
                                            style="">Aftercare</td>



                                        <td class="py-2 px-3">-</td>
                                        <td class="py-2 px-3">-</td>
                                        <td class="py-2 px-3">-</td>

                                    </tr>

                                </tbody>

                            </table>
                        </div>








                    </div>


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
                <div class="border-b pb-4 mb-3 last-of-type:border-b-0 border-b-gray-300">
                    <h3>Team 0</h3>
                    <p><iframe src="https://giphy.com/embed/JIX9t2j0ZTN9S" class="w-full" frameBorder="0"
                            class="giphy-embed" allowFullScreen></iframe>

                    </p>
                </div>
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


    <script>
        function submissionCheck() {

            const mpIds = [1, 2];

            let allGood = true;

            mpIds.forEach(id => {
                let checked = document.querySelector(`input[name="mp-${id}"]:checked`);

                if (!checked) {
                    let el = document.getElementById(`mpcontainer-${id}`);
                    if (allGood) {
                        el.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center',
                            inline: 'center'
                        });
                    }

                    allGood = false;



                    // Highlight the missing marking point

                    el.classList.add('border-2', 'rounded-md',
                        'border-bulsca_red', 'p-2',
                        'animate-pulse');


                    el.querySelectorAll('input').forEach(input => {
                        input.onclick = () => {
                            el.classList.remove('border-2', 'rounded-md',
                                'border-bulsca_red', 'p-2',
                                'animate-pulse');

                        }
                    })
                }



            });

            return allGood;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/intro.js@7.2.0/intro.min.js"></script>
    <script>
        let ij = introJs().setOption("exitOnOverlayClick", false).setOption('disableInteraction', true)

        ij.start()
    </script>
@endsection
