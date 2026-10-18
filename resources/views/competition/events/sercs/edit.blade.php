@extends('layouts.competition')

@section('title')
    (Edit) {{ $serc->name }}
@endsection



@section('content')
    <x-serc-editor :serc="$serc" :edit="true" />
@endsection
