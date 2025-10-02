@extends('layouts.competition')

@section('title')
    Add Speeds Event
@endsection



@section('content')
    <h2>Add Speed Event</h2>
    <br>
    <form action="{{ route('comps.view.events.speeds.addPost', $comp) }}" method="post">
        @csrf
        <div class="grid-4">

            <div class="se-form-input" style="margin-bottom: 0 !important">
                <label for="">Speed Event</label>

                <select style="margin-bottom: 0 !important" name="event" required>
                    <option value="" selected disabled>Please select an event...</option>
                    @foreach (App\Models\SpeedEvent::get() as $option)
                        <option value="{{ $option->id }}">
                            {{ $option->name }}</option>
                    @endforeach
                </select>
            </div>


            <div class="se-form-input" style="margin-bottom: 0 !important">
                <label for="">Target</label>

                <select style="margin-bottom: 0 !important" name="target_entity" required>
                    <option value="club">Clubs</option>
                    <option value="team" selected>Teams</option>
                    <option value="competitor">Competitiors</option>
                </select>
            </div>

            <input type="number" name="weight" value="1" required class="hidden">
            <input type="text" name="record" value="00:00.000" required class="hidden">


        </div>
        <br>

        <button type="submit" class="se-btn se-btn-success">Add</button>
    </form>
@endsection
