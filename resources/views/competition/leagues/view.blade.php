@extends('layouts.competition')

@section('title')
    {{ $league->name }}
@endsection



@section('content')
    <h2>{{ $league->name }}</h2>
    <br>
    <form action="{{ route('comps.leagues.update', [$comp, $league]) }}" method="post">
        @csrf
        <div class="grid-4">

            <div class="se-form-input" style="margin-bottom: 0 !important">
                <label for="">Name</label>

                <input type="text" name="name" value="{{ $league->name }}" required placeholder="League name">
            </div>



        </div>
        <br>

        <button type="submit" class="se-btn se-btn-success">Save</button>
    </form>
    <br>
    <hr class="spacer">
    <br>
    <form action="{{ route('comps.leagues.delete', [$comp, $league]) }}" method="post"
        @submit="doConfirm($event, 'Are you sure you want to delete this league?')">
        @method('delete')
        @csrf

        <button class="se-btn se-btn-danger">Delete</button>
    </form>
@endsection
