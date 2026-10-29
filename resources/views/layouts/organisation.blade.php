@extends('layouts.core')

@section('core-title')
    @hasSection('title')
        @yield('title') |
    @endif
    {{ $org->name }}
@endsection

@section('core-meta')
    {{-- No type: logos are PNG, JPEG or WebP, and browsers sniff it --}}
    <link rel="icon" href="{{ $org->getLogo() }}">
@endsection

@section('core-content')
    <div class="flex  items-center justify-between">
        <div class="flex flex-col-reverse md:flex-row md:items-center justify-between gap-2 md:gap-0 w-full -mb-2">
            <h1 class="">{{ $org->name }}</h1>
            <img src="{{ $org->getLogo() }}" alt="" class="size-14 rounded-full">
        </div>




    </div>


    {{-- The same tabs as the Svelte pages' (App\Helpers\OrganisationTabs) --}}
    <div class="tabbed-bar mt-2 mb-4">
        @foreach (App\Helpers\OrganisationTabs::for($org) as $tab)
            <a href="{{ $tab['url'] }}" class="@if ($tab['current']) active @endif">{{ $tab['label'] }}</a>
        @endforeach
    </div>

    @yield('content')
@endsection
