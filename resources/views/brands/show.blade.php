@extends('layout')

@section('title')
    Create Brand | Admin
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.index') }}">Admin</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.brands') }}">Brands</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.brands.show', $brand) }}">{{ $brand->name }}</a>
    </div>
@endsection

@section('content')

    <style>
        :root {
            --brand-primary: {{ $brand->primary_color }};
            --brand-secondary: {{ $brand->secondary_color }};
        }
    </style>
    <br>
    <div class="grid-4">
        <div class="flex flex-col items-center justify-center">
            <img src="{{ asset('storage/'.$brand->logo) }}" alt="{{ $brand->name }}" class="max-w-32 max-h-32">
            <h5 class="mb-0">{{ $brand->name }}</h5>
    
        </div>
        <div class="flex flex-col items-center justify-center">
            <h5 class="mb-0">{{ $brand->website }}</h5>
            <a href="mailto:{{ $brand->email }}" class="mb-0">{{ $brand->email }}</a>
        </div>
        <div class="flex flex-col items-center justify-center">
            <div class="w-8 h-8 rounded-full" style="background-color: var(--brand-primary)"></div>
            <h5 class="mb-0">Primary Colour</h5>
        </div>
        <div class="flex flex-col items-center justify-center">
            <div class="w-8 h-8 rounded-full" style="background-color: var(--brand-secondary)"></div>
            <h5 class="mb-0">Secondary Colour</h5>
        </div>
    </div>
    <br>
    <hr>
    <br>
    <h4 class="mb-0">Edit Brand</h4>
    <br>
    <form action="{{ route('admin.brands.update', $brand) }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="grid-4">
            <x-form-input id="name" title="Name" required placeholder="MyBrand" defaultValue="{{ $brand->name }}"></x-form-input>
            <x-form-input id="website" title="Website" required placeholder="mybrand.com" defaultValue="{{ $brand->website }}"></x-form-input>


            <x-form-input id="email" title="Email" required type="email"
                placeholder="hello@mybrand.com" defaultValue="{{ $brand->email }}"></x-form-input>

            <x-form-input id="logo" title="Logo" type="file" required="false"></x-form-input>

            <x-form-input id="primary_color" title="Primary Colour" required type="color" defaultValue="{{ $brand->primary_color }}">
                <script>
                    (() => {
                        const input = document.getElementById('form-link-primary_color');

                        input.addEventListener('input', function() {
                            // Update the --brand-primary CSS variable with the new colour
                            document.documentElement.style.setProperty('--brand-primary', input.value);
                        });
                    })()
                </script>
            </x-form-input>
            <x-form-input id="secondary_color" title="Secondary Colour" required type="color" defaultValue="{{ $brand->secondary_color }}">
                <script>
                    (() => {
                        const input = document.getElementById('form-link-secondary_color');

                        input.addEventListener('input', function() {
                            // Update the --brand-primary CSS variable with the new colour
                            document.documentElement.style.setProperty('--brand-secondary', input.value);
                        });
                    })()
                </script>
            </x-form-input>

         



        </div>
        <button type="submit" class="btn">Update</button>
    </form>
@endsection
