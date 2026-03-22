@extends('layouts.organisation')

@section('title', '403')

@section('content')
    <div class="flex flex-col h-82 items-center justify-center">
        <h1 class="text-7xl! font-extrabold! ">403</h1>
        <h4>Foribbden</h4>
        <br>
        <p class="text-center">You <strong>aren't permitted</strong> to view/do this.<br><strong>Please select one of the
                available tabs
                above</strong>.
        </p>
        <br>
        <a href="{{ url()->previous() }}" class="se-btn">Back</a>
    </div>
@endsection
