@extends('layout')

@section('title')
    Admin
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.index') }}">Admin</a>
    </div>
@endsection

@section('content')
    <h2 class="mb-0">Brands</h2>
    <br>
    <div class="grid-4">
        @php
            $brands = \App\Models\Brands\Brand::orderBy('name')->paginate(8);
        @endphp
        @foreach ($brands as $brand)
            <a href="{{ route('admin.brands.show', $brand) }}"
                class="flex flex-row items-center shadow-md hover:shadow-lg bg-white p-4 rounded-md border hover:border-black transition-all space-x-2">
                <img src="{{ $brand->getLogo() }}" alt="" class="w-8 h-8">
                <h5 class="mb-0">{{ $brand->name }}</h5>
            </a>
        @endforeach
        <x-add-card text="Brand" link="{{ route('admin.brands.create') }}"></x-add-card>


    </div>
    {{ $brands->links() }}

@endsection
