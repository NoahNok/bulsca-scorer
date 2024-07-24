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
        <a href="{{ route('admin.brands.create') }}">Create Brand</a>
    </div>
@endsection

@section('content')
    <h2 class="mb-0">Create Brand</h2>
    <br>
    <form action="{{ route('admin.brands.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="grid-4">
            <x-form-input id="name" title="Name" required placeholder="MyBrand"></x-form-input>
            <x-form-input id="website" title="Website" required placeholder="mybrand.com"></x-form-input>


            <x-form-input id="email" title="Email" required type="email"
                placeholder="hello@mybrand.com"></x-form-input>

            <x-form-input id="logo" title="Logo" type="file" required></x-form-input>

            <x-form-input id="primary_color" title="Primary Colour" required type="color">
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
            <x-form-input id="secondary_color" title="Secondary Colour" required type="color">
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
        <button type="submit" class="btn">Add</button>
    </form>
@endsection
