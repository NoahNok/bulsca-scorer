<!DOCTYPE html>
<html lang="en">



<head>
    <meta charset="UTF-8">


    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="manifest" href="/manifest.json">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @hasSection('core-title')
            @yield('core-title') |
        @endif Scoring.Events
    </title>

    @yield('core-meta')


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Anton&family=Archivo:ital,wght@0,100..900;1,100..900&family=Oswald:wght@300;400;700&display=swap"
        rel="stylesheet">


    @vite('resources/js/app.js')
    @vite('resources/css/app.css')
    {{-- <link rel="stylesheet" href="{{ asset('css/app.css') }}?{{ config('version.hash') }}"> --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.13.3/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>


    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    @auth
        <script src="{{ asset('sw.js') }}"></script>
    @endauth

    <script>
        document.addEventListener('alpine:init', () => {
            console.log('hi')
            Alpine.directive('tabbed', (el, {
                expression
            }, {
                evaluateLater
            }) => {
                // Initialize Alpine state for tabs
                const tabs = Array.from(el.children).map(child => child.innerText.trim());

                let runCallback = expression ? evaluateLater(expression) : null;

                const initialState = {
                    activeTab: tabs[0] || null
                }; // Default to first tab






                // Apply the state to the container
                el.setAttribute('x-data', JSON.stringify(initialState));

                Array.from(el.children).forEach(tab => {
                    const tabName = tab.innerText.trim();

                    // Add Alpine bindings dynamically
                    tab.setAttribute('x-on:click', `activeTab = '${tabName}';`);
                    tab.setAttribute(':class', `{ 'active': activeTab === '${tabName}' }`);


                    tab.addEventListener('click', () => {
                        if (runCallback) {
                            runCallback(() => {
                                return this.activeTab
                            });
                        }
                    });
                });



            });



        });
    </script>



</head>

<body class="flex flex-col overflow-x-hidden selection:bg-se selection:text-white " x-data="{
    asideCollapsed: false,
    modals: {
        data: {}
    },

    customConfirm(message) {
        return new Promise((resolve) => {
            this.modals.confirmDialog = true;
            this.modals.data.confirmDialog.message = message;


            $watch('modals.data.confirmDialog.action', (newVal) => {
                console.log('watched', newVal);
                if (!newVal) {
                    resolve(false);
                }
                this.modals.confirmDialog = false;

                resolve(true)
            });
        });
    },

    async doConfirm(e, message) {

        e.preventDefault();
        const confirmed = await this.customConfirm(message);
        if (confirmed) {
            // Now you can manually submit the form or proceed with your logic

            e.target.submit(); // Optional: manually submit the form
        }
    },

    async askConfirm(message) {
        const confirmed = await this.customConfirm(message);
        return confirmed;
    }
}">




    <header class="bg-white z-10 shadow-sm" x-data="{ navOpen: false }">
        <nav class="mx-auto flex  items-center justify-between p-4 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1 items-center space-x-10">
                <!-- <a href="#" class="-m-1.5 p-1.5 text-red-500">
                    <span class="sr-only">Your Company</span>
                    <img class="h-8 w-auto" src="se-cc.svg" class=" " alt="">
                </a> -->
                <a href="/">
                    <h3 class="ml-3 text-xl font-archivo font-semibold group hover:text-se! transition-colors">
                        Scoring.<span class="text-se group-hover:text-black! transition-colors">Events</span></h3>
                </a>

                <div class="hidden lg:flex space-x-10 items-center">
                    <a href="{{ route('explore') }}"
                        class="text-sm/6 font-semibold text-gray-900 hover:text-se transition-colors">Explore</a>
                    <a href="{{ route('dj.home') }}"
                        class="text-sm/6 font-semibold text-se hover:text-se-accent transition-colors">Officials</a>
                </div>


            </div>
            <div class="flex lg:hidden">
                <button type="button"
                    class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                    @click="navOpen = true">
                    <span class="sr-only">Open main menu</span>
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>





            @auth

                <div class="hidden lg:flex lg:flex-1 lg:justify-end space-x-3 ">

                    @php
                        $orgs = Auth::user()->getOrganisations;
                    @endphp

                    @if (count($orgs) > 0)
                        <button
                            class="text-sm/6 font-semibold text-gray-900  hover:bg-gray-300 px-2 pr-1  rounded-md transition-colors flex items-center space-x-2 relative cursor-pointer group">




                            @if (Str::startsWith(Route::currentRouteName(), 'orgs') && isset($org))

                                {{ $org->name }}
                            @elseif (
                                (Str::startsWith(Route::currentRouteName(), 'comps') || Str::startsWith(Route::currentRouteName(), 'landing.')) &&
                                    isset($comp) &&
                                    $comp->getOrganisation)
                                {{ $comp->getOrganisation?->name }}
                            @else
                                Organisation
                            @endisset
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-4 mx-1 cursor-pointer">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                            </svg>



                            <div
                                class="absolute top-6 -left-[25%] w-[150%]  bg-white border-2 rounded-md hidden group-hover:block group-focus:block group-focus-within:block">
                                @foreach ($orgs as $orgg)
                                    <a href="{{ route('orgs.show', $orgg->name) }}"
                                        class="hover:bg-gray-200 px-2 py-1 text-sm/6 flex items-center justify-between">
                                        {{ $orgg->name }}

                                        <img src="{{ $orgg->getLogo() }}" class="size-5" alt="">
                                    </a>
                                @endforeach
                            </div>
                    </button>
                @endif


                <a href="{{ route('home') }}"
                    class="text-sm/6 font-semibold text-gray-900 hover:text-se transition-colors">Dashboard</a>
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('admin.index') }}"
                        class="text-sm/6 font-semibold text-gray-900 hover:text-se transition-colors">Admin</a>
                @endif
                <a href="{{ route('logout') }}"
                    class="text-sm/6 font-semibold text-gray-900 hover:text-se transition-colors">Sign out <span
                        aria-hidden="true">&rarr;</span></a>
            </div>
        @else
            <div class="hidden lg:flex lg:flex-1 lg:justify-end space-x-4">

                <a href="{{ route('register') }}"
                    class="text-sm/6 font-semibold text-se hover:text-se-accent transition-colors">Sign up <span
                        aria-hidden="true">&uarr;</span></a>

                <a href="{{ route('login') }}"
                    class="text-sm/6 font-semibold text-gray-900 hover:text-se transition-colors">Sign in <span
                        aria-hidden="true">&rarr;</span></a>
            </div>
        @endauth


    </nav>
    <!-- Mobile menu, show/hide based on menu open state. -->
    <div class="lg:hidden" x-show="navOpen" role="dialog" aria-modal="true">
        <!-- Background backdrop, show/hide based on slide-over state. -->
        <div class="fixed inset-0 z-10"></div>
        <div
            class="fixed inset-y-0 right-0 z-50! w-full overflow-y-auto bg-white px-6 py-4 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
            <div class="flex items-center justify-between">
                <a href="#" class="">
                    <h3 class="ml-1 text-xl font-archivo font-semibold group hover:text-se! transition-colors">
                        Scoring.<span class="text-se group-hover:text-black! transition-colors">Events</span></h3>
                </a>
                <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700" @click="navOpen = false">
                    <span class="sr-only">Close menu</span>
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" aria-hidden="true" data-slot="icon">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-6 flow-root mx-1">
                <div class="-my-6 divide-gray-500/10">




                    <div class="space-y-2 py-6 ">

                        @auth
                            @if (count($orgs) > 0)
                                <button
                                    class="text-sm/6 font-semibold text-gray-900  bg-gray-200 hover:bg-gray-300 scale-105 w-full  px-2 py-1   rounded-md transition-colors flex items-center justify-between space-x-2 relative cursor-pointer group">




                                    @if (Str::startsWith(Route::currentRouteName(), 'orgs') && isset($org))

                                        {{ $org->name }}
                                    @elseif (
                                        (Str::startsWith(Route::currentRouteName(), 'comps') || Str::startsWith(Route::currentRouteName(), 'landing.')) &&
                                            isset($comp) &&
                                            $comp->getOrganisation)
                                        {{ $comp->getOrganisation?->name }}
                                    @else
                                        Organisation
                                    @endisset
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-4 mx-1 cursor-pointer">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 15 12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                    </svg>



                                    <div
                                        class="absolute top-9 w-[104%] -left-[2%] bg-white border-2 rounded-md hidden group-hover:block group-focus:block group-focus-within:block group-active:block">
                                        @foreach ($orgs as $orgg)
                                            <a href="{{ route('orgs.show', $orgg->name) }}"
                                                class="hover:bg-gray-200 px-3 py-2 text-sm/6 flex items-center justify-between">
                                                {{ $orgg->name }}

                                                <img src="{{ $orgg->getLogo() }}" class="size-5" alt="">
                                            </a>
                                        @endforeach
                                    </div>
                            </button>
                        @endif
                    @endauth

                    <a href="{{ route('explore') }}"
                        class="-mx-3 block rounded-lg px-3 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Explore</a>

                    <a href="{{ route('dj.home') }}"
                        class="-mx-3 block rounded-lg px-3 text-se font-semibold text-gray-900 hover:bg-gray-50">Officials
                        Login</a>



                    @auth


                        <a href="{{ route('home') }}"
                            class="-mx-3 block rounded-lg px-3  text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Dashboard</a>

                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('admin.index') }}"
                                class="-mx-3 block rounded-lg px-3 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Admin</a>
                        @endif

                        <hr class="spacer my-4!">

                        <a href="{{ route('logout') }}"
                            class="-mx-3 block rounded-lg px-3 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Sign
                            out <span aria-hidden="true">&rarr;</span></a>
                    @else
                        <hr class="spacer my-4!">
                        <a href="{{ route('register') }}"
                            class="-mx-3 block rounded-lg px-3 text-base/7 font-semibold text-se hover:bg-gray-50">Sign
                            up <span aria-hidden="true">&uarr;</span></a>
                        <a href="{{ route('login') }}"
                            class="-mx-3 block rounded-lg px-3 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Sign
                            in <span aria-hidden="true">&rarr;</span></a>
                    @endauth
                </div>

            </div>
        </div>
    </div>
</div>
</header>




<main class="flex w-screen justify-center my-8 md:my-16">
<div class=" w-[90%] lg:w-[85%] 3xl:w-[65%] sm:px-6 md:px-8 flex flex-col " x-data="{
    global_state: {}
}">

    @yield('core-content')




    <x-s-e-modal id="confirmDialog" title="Confirm" x-init="() => {
        onClose = () => {
            modals.data.confirmDialog.action = false;
        }
    }">
        <p x-text="modals.data.confirmDialog.message ?? ''">Are you sure?</p>
        <x-slot name="footer">
            <button class="se-btn se-btn-success"
                @click="modals.data.confirmDialog.action = true">Yes</button>
        </x-slot>
    </x-s-e-modal>
</div>
</main>







<div class="alert-banner" id="alert">Test</div>



<script src="{{ asset('js/editable-table.js') }}"></script>
<script src="{{ asset('js/alert.js') }}"></script>
<script src="{{ asset('js/serc-builder.js') }}"></script>
<script>
    window.onload = () => {

        document.querySelectorAll("[serc-builder]").forEach(sb => {

            new SERCBuilder(sb);
        })
        document.querySelectorAll("[editable-table]").forEach(et => {
            new EditableTable(et)
        })

        function convertUTCMinutesToLocal() {
            const inputs = document.querySelectorAll('input[type="datetime-local"]');

            inputs.forEach(input => {
                const rawUTC = input.value;
                if (!rawUTC) return;

                // Parse the UTC datetime string
                const utcDate = new Date(rawUTC + 'Z'); // Add 'Z' to mark it as UTC

                // Convert to local time
                utcDate.setMinutes(utcDate.getMinutes() - utcDate.getTimezoneOffset());

                // Format for datetime-local input (YYYY-MM-DDTHH:mm)
                const localValue = utcDate.toISOString().slice(0, 16);

                // Update the input
                input.value = localValue;
            });
        }


        convertUTCMinutesToLocal()



    }
</script>
@if (Session::has('success'))
<script>
    showSuccess('{{ Session::get('success') }}')
</script>
@endif
@if (Session::has('alert-error'))
<script>
    showAlert('{{ Session::get('alert-error') }}')
</script>
@endif
</body>

</html>
