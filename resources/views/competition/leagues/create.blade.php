@extends('layouts.competition')

@section('title')
    Create League
@endsection



@section('content')
    <h2>Create League</h2>
    <br>
    <form action="{{ route('comps.leagues.store', $comp) }}" method="post">
        @csrf
        <div class="grid-4">

            <div class="se-form-input" style="margin-bottom: 0 !important">
                <label for="">Name</label>

                <input type="text" name="name" required placeholder="League name">
            </div>

         

        </div>
        <br>

        <button type="submit" class="se-btn se-btn-success">Create</button>
    </form>
@endsection
