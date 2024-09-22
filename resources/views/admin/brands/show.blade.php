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
            <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" class="max-w-32 max-h-32">
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
            <x-form-input id="name" title="Name" required placeholder="MyBrand"
                defaultValue="{{ $brand->name }}"></x-form-input>
            <x-form-input id="website" title="Website" required placeholder="mybrand.com"
                defaultValue="{{ $brand->website }}"></x-form-input>


            <x-form-input id="email" title="Email" required type="email" placeholder="hello@mybrand.com"
                defaultValue="{{ $brand->email }}"></x-form-input>

            <x-form-input id="logo" title="Logo" type="file" required="false"></x-form-input>

            <x-form-input id="primary_color" title="Primary Colour" required type="color"
                defaultValue="{{ $brand->primary_color }}">
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
            <x-form-input id="secondary_color" title="Secondary Colour" required type="color"
                defaultValue="{{ $brand->secondary_color }}">
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
    <br><br>
    <h3 class="mb-0">Brand Users</h3>

    <div class="  relative w-full  ">
        <table class=" text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
            <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                <tr>
                    <th scope="col" class="py-3 px-6 text-left">
                        Name
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Associated Competition
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Role
                    </th>
                    <th scope="col" class="py-3 px-6">
                        Actions
                    </th>

                </tr>
            </thead>
            <tbody>

                @forelse ($brand->getUsers->sortBy([['pivot.role', 'asc'], ['name', 'asc']]) as $user)
                    <tr class="bg-white border-b text-right " x-data="{
                        resetPassword() {
                                if (!confirm('Are you sure you want to reset this users password?')) return
                    
                                fetch('{{ route('admin.brands.users.reset-password', [$brand, $user]) }}').then(resp => resp.json()).then(data => {
                                    if (data.password) {
                                        navigator.clipboard.writeText(data.password)
                                        showSuccess('Password copied to clipboard.')
                                    } else {
                                        showAlert('Failed to reset password. Try again later.')
                    
                                    }
                    
                                })
                            },
                    
                    }">
                        <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                            {{ $user->name }} ({{ $user->email }})
                        </th>
                        <td class="py-4 px-6">
                            @if ($user->competition)
                                <a class="link" href="{{ route('comps.view', $user->getCompetition) }}">
                                    {{ $user->getCompetition->name }}</a>
                            @else
                                -
                            @endif


                        </td>
                        <td class="py-4 px-6 capitalize">
                            {{ $user->pivot->role }}

                        </td>
                        <td class="py-4 px-6 ">
                            <div class="flex items-end justify-end space-x-3">


                                @if (!$user->competition && $user->id != Auth::user()->id)
                                    <div title="Delete account" class="flex items-center justify-center h-full">

                                        <form method="POST" class="flex items-center justify-center h-full"
                                            onsubmit="return confirm('Are you sure you want to delete this brand user? (It cannot be undone!)')"
                                            action="{{ route('admin.brands.users.delete', [$brand, $user]) }}">
                                            @csrf
                                            <button type="submit">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                    class="size-6 hover:text-black cursor-pointer">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>



                                    </div>
                                @endif


                                <div title="Reset Password" @click="resetPassword()">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-6 hover:text-black cursor-pointer">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1 1 21.75 8.25Z" />
                                    </svg>
                                </div>


                            </div>




                        </td>

                    </tr>
                @empty
                    <tr class="bg-white border-b text-right ">
                        <th colspan="100" scope="row"
                            class="py-4  px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
                            None
                        </th>
                    </tr>
                @endforelse



            </tbody>
        </table>
    </div>

    <div class="w-1 h-2"></div>
    <h5>Add User</h5>
    <form class="grid-4" method="POST" action="{{ route('admin.brands.users.create', $brand) }}"
        @submit="(e) => {
            console.log($refs.accountRole)
        if ($refs.accountRole.value === 'null') {
            e.preventDefault()
            $refs.accountRole.setCustomValidity('Please select a role.')
            $refs.accountRole.reportValidity()
            
        } 
    }">
        @csrf
        <x-form-input id="accountName" title="" required placeholder="Account name"></x-form-input>
        <x-form-input id="accountEmail" title="" required placeholder="Account Email"></x-form-input>
        <div class="form-input select">
            <label for="accountRole"></label>
            <select name="accountRole" id="" x-ref="accountRole" required
                @change="(e) => e.target.setCustomValidity('')" style="padding-top: 0.65em; padding-bottom: 0.75em;">
                <option value="null">Please select a role</option>
                <option value="admin" @if (old('accountRole') == 'admin') selected @endif>Admin</option>
                <option value="welfare" @if (old('accountRole') == 'welfare') selected @endif>Welfare</option>
            </select>
        </div>
        <div class="">

            <button class="btn ml-auto mt-2 w-full">Add</button>

        </div>
    </form>

    <br><br>
    <h3 class="mb-0">Delete Brand</h3>
    <br>
    <form action="{{ route('admin.brands.delete', $brand) }}"
        onsubmit="return confirm('This action cannot be undone! Are you sure?')" method="post">
        @csrf
        @method('DELETE')


        <input type="hidden" name="brand" value="{{ $brand->id }}">

        <button type="submit" class="btn btn-danger">Delete</button>
    </form>


    @if (Session::has('brand-password'))
        <script>
            navigator.clipboard.writeText('{{ Session::get('brand-password') }}')
            setTimeout(() => {
                alert(
                    'A Brand account has been created and its password has been copied to you clipboard.'
                )
            }, 500);
        </script>
    @endif
@endsection
