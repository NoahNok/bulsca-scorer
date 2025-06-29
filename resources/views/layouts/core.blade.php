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

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
    @auth
        <script src="{{ asset('js/enable-push.js') }}"></script>
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

<body class="flex flex-col overflow-x-hidden " x-data="{
    asideCollapsed: false,
    modals: {
        data: {}
    }
}">


    @isset($brand)
        <style>
            :root {
                --brand-primary: {{ $brand->primary_color }};
                --brand-secondary: {{ $brand->secondary_color }};
            }
        </style>
    @endisset


    <header class="bg-white z-10 shadow-sm" x-data="{ navOpen: false }">
        <nav class="mx-auto flex  items-center justify-between p-4 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1 items-center">
                <!-- <a href="#" class="-m-1.5 p-1.5 text-red-500">
                    <span class="sr-only">Your Company</span>
                    <img class="h-8 w-auto" src="se-cc.svg" class=" " alt="">
                </a> -->
                <a href="/">
                    <h3 class="ml-3 text-xl font-archivo font-semibold">Scoring.<span class="text-se">Events</span></h3>
                </a>


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
                @if (Auth::user()->isAdmin())
                    <div class="hidden lg:flex lg:flex-1 lg:justify-end space-x-3">
                        <a href="{{ route('admin.index') }}" class="text-sm/6 font-semibold text-gray-900">Admin</a>
                        <a href="{{ route('logout') }}" class="text-sm/6 font-semibold text-gray-900">Logout</a>
                    </div>
                @endif
            @else
                <div class="hidden lg:flex lg:flex-1 lg:justify-end">
                    <a href="{{ route('login') }}" class="text-sm/6 font-semibold text-gray-900 hover:text-se">Log in <span
                            aria-hidden="true">&rarr;</span></a>
                </div>
            @endauth


        </nav>
        <!-- Mobile menu, show/hide based on menu open state. -->
        <div class="lg:hidden" x-show="navOpen" role="dialog" aria-modal="true">
            <!-- Background backdrop, show/hide based on slide-over state. -->
            <div class="fixed inset-0 z-10"></div>
            <div
                class="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white px-6 py-4 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                <div class="flex items-center justify-between">
                    <a href="#" class="-m-1.5 p-1.5">
                        <span class="sr-only">Your Company</span>
                        <img class="h-8 w-auto"
                            src="https://tailwindui.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600"
                            alt="">
                    </a>
                    <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700" @click="navOpen = false">
                        <span class="sr-only">Close menu</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            aria-hidden="true" data-slot="icon">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-2 py-6 ">

                            <a href="#"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Manage</a>
                            <a href="#"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Judge</a>
                            <a href="#"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Results</a>
                        </div>
                        <div class="py-6">
                            <a href="#"
                                class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Log
                                in</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>




    <main class="flex w-screen justify-center my-8 md:my-16">
        <div class="w-screen sm:w-[90%] md:w-[75%] px-6 md:px-8 flex flex-col ">
            @yield('core-content')
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

            document.getElementById("mobile-nav-opener").onclick = (e) => {
                document.getElementById('nav').classList.toggle('open');
            }

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
