@extends('layouts.competition')

@section('title')
    Edit | {{ $event->getName() }}
@endsection



@section('content')
    <h2>{{ $event->getName() }}</h2>
    <br>
    <form action="{{ route('comps.events.speeds.editPost', [$comp, $event]) }}" onsubmit="return confirm('Are you sure?')"
        method="post">
        @csrf
        <div class="grid-4">




            <div>
                <p class="text-red-500 font-semibold mb-1">Changing the target type will delete all current results!</p>
                <div class="se-form-input" style="margin-bottom: 0 !important">
                    <label for="">Target</label>

                    <select style="margin-bottom: 0 !important" name="target_entity" required>
                        <option value="club" @if ($event->scorable_entity == 'club') selected @endif>Clubs</option>
                        <option value="team" @if ($event->scorable_entity == 'team') selected @endif>Teams</option>
                        <option value="competitor" @if ($event->scorable_entity == 'competitor') selected @endif>Competitiors</option>
                    </select>


                </div>

            </div>

            <input type="number" name="weight" value="1" required class="hidden">
            <input type="text" name="record" value="00:00.000" required class="hidden">


        </div>
        <br>

        <button type="submit" class="se-btn se-btn-success">Save</button>
    </form>
@endsection
