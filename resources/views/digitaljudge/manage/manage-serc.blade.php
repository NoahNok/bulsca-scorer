@extends('digitaljudge.mpa-layout')

@section('title')
    {{ $serc->getName() }}
@endsection
@php
    $backlink = route('dj.manage.index');
    $icon =
        ' <path stroke-linecap="round" stroke-linejoin="round" d="M16.712 4.33a9.027 9.027 0 011.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 00-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 010 9.424m-4.138-5.976a3.736 3.736 0 00-.88-1.388 3.737 3.737 0 00-1.388-.88m2.268 2.268a3.765 3.765 0 010 2.528m-2.268-4.796a3.765 3.765 0 00-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 01-1.388.88m2.268-2.268l4.138 3.448m0 0a9.027 9.027 0 01-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0l-3.448-4.138m3.448 4.138a9.014 9.014 0 01-9.424 0m5.976-4.138a3.765 3.765 0 01-2.528 0m0 0a3.736 3.736 0 01-1.388-.88 3.737 3.737 0 01-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 01-1.652-1.306 9.027 9.027 0 01-1.306-1.652m0 0l4.138-3.448M4.33 16.712a9.014 9.014 0 010-9.424m4.138 5.976a3.765 3.765 0 010-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 011.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 00-1.652 1.306A9.025 9.025 0 004.33 7.288" />';

@endphp

@section('content')
    <div x-data="{
        open: false,
    
        mp: {
            id: 0,
            name: '',
            weight: 0,
        },
    
        openPopup(id, name, weight) {
            this.mp.id = id;
            this.mp.name = name;
            this.mp.weight = weight;
            this.open = true;
        },
    
        toggle() {
            this.open = !this.open
        }
    }">
        <p>You can manage SERC weightings below. To edit a weighting click anywhere in on the marking point or weighting and
            use
            the popup to modify it.</p>


        @foreach ($serc->getJudges as $judge)
            <h4>{{ $judge->name }}</h4>

            <div class="border shadow-md bg-white rounded-md flex flex-col transition-all mt-2 mb-4">
                @foreach ($judge->getMarkingPoints as $mp)
                    <div class="flex justify-between items-center hover:bg-gray-100 transition-colors group cursor-pointer px-5 py-3"
                        x-on:click="openPopup({{ $mp->id }}, '{{ $mp->name }}' , {{ $mp->weight }})">
                        <p class="text-bulsca group-hover:text-bulsca_red font-semibold">{{ $mp->name }}</p>
                        <p class="group-hover:font-bold">{{ $mp->weight }}</p>
                    </div>
                @endforeach
            </div>
        @endforeach


        <div class="fixed left-0 top-0 w-screen h-screen bg-gray-100 bg-opacity-50 flex items-center justify-center"
            x-show="open" style="display: none" @click="toggle()">
            <div class="card w-[90%] md:w-[20%]" @click.stop>
                <div class="flex items-center justify-between">
                    <h4 class="hmb-0">Edit Marking Point</h4>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 transition-transform hover:rotate-90 cursor-pointer"
                        @click="toggle()">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>

                </div>

                <form method="POST"
                    onsubmit="return confirm('Are you sure you want to change the weighting of this marking point? This action will be recorded!')"
                    class="flex flex-col space-y-2">
                    @csrf
                    <input type="hidden" name="id" x-model="mp.id">
                    <p x-text="mp.name"></p>



                    <div class="form-input">
                        <label for="" class="">Weight</label>
                        <input class="input" name="weight" step=".1" type="number" x-model="mp.weight"
                            @keyup.enter="addClub()">
                    </div>
                    <button type="submit" class="btn btn-success">Save</button>
                </form>

            </div>

        </div>
    </div>
@endsection
