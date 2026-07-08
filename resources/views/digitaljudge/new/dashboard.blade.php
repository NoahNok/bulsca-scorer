@extends('digitaljudge.mpa-layout')
@section('title')
    Dashboard
@endsection
@php
    $backlink = false;
    $icon = '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />

';
@endphp
@section('content')
    <div class="flex flex-col space-y-3">
        <h1 class="text-2xl font-bold">Welcome, {{ \App\DigitalJudge\DigitalJudge::getClientName() }}!</h1>
        <p class="text-gray-700">This is your dashboard where you can view and manage your judging activities.</p>

   
        

    </div>

    <div class="text-center mt-6">
        <a href="{{ route('dj.logout') }}" class="link">Logout</a>
    </div>
@endsection

