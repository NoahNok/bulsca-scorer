@extends('layouts.organisation')



@section('content')
    <h2 class="mb-0">Competitions</h2>
    <br>
    <div>
        @php
            $comps = $org->getCompetitions()->paginate(18);
        @endphp

        <div class="se-table se-table-thin">
            <table>

                <tbody>
                    @foreach ($comps as $comp)
                        <tr class="">
                            <td class="text-left font-semibold text-black relative">{{ $comp->name }} @if ($comp->canUser(auth()->user(), 'admin'))
                                    <span class="ml-2 badge badge-info badge-sm">ADMIN</span>
                                @endif <a href="{{ route('comps.view', $comp) }}"
                                    class="absolute top-0 left-0 w-full h-full"></a>
                            </td>

                            <td class=" flex items-center justify-end gap-6">
                                <div class="flex items-center  gap-3">
                                    @if (!$comp->show_competition)
                                        <div title="This competition is private">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex items-center justify-center gap-1" title="Speed Events #">
                                        {{ $comp->getSpeedEvents->count() }} <svg xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                        </svg>
                                    </div>
                                    <div class="flex items-center justify-center gap-1" title="SERC Events #">
                                        {{ $comp->getSERCs->count() }}
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.712 4.33a9.027 9.027 0 0 1 1.652 1.306c.51.51.944 1.064 1.306 1.652M16.712 4.33l-3.448 4.138m3.448-4.138a9.014 9.014 0 0 0-9.424 0M19.67 7.288l-4.138 3.448m4.138-3.448a9.014 9.014 0 0 1 0 9.424m-4.138-5.976a3.736 3.736 0 0 0-.88-1.388 3.737 3.737 0 0 0-1.388-.88m2.268 2.268a3.765 3.765 0 0 1 0 2.528m-2.268-4.796a3.765 3.765 0 0 0-2.528 0m4.796 4.796c-.181.506-.475.982-.88 1.388a3.736 3.736 0 0 1-1.388.88m2.268-2.268 4.138 3.448m0 0a9.027 9.027 0 0 1-1.306 1.652c-.51.51-1.064.944-1.652 1.306m0 0-3.448-4.138m3.448 4.138a9.014 9.014 0 0 1-9.424 0m5.976-4.138a3.765 3.765 0 0 1-2.528 0m0 0a3.736 3.736 0 0 1-1.388-.88 3.737 3.737 0 0 1-.88-1.388m2.268 2.268L7.288 19.67m0 0a9.024 9.024 0 0 1-1.652-1.306 9.027 9.027 0 0 1-1.306-1.652m0 0 4.138-3.448M4.33 16.712a9.014 9.014 0 0 1 0-9.424m4.138 5.976a3.765 3.765 0 0 1 0-2.528m0 0c.181-.506.475-.982.88-1.388a3.736 3.736 0 0 1 1.388-.88m-2.268 2.268L4.33 7.288m6.406 1.18L7.288 4.33m0 0a9.024 9.024 0 0 0-1.652 1.306A9.025 9.025 0 0 0 4.33 7.288" />
                                        </svg>

                                    </div>
                                </div>
                                <div>{{ $comp->when->format('d/m/Y') }}</div>

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <x-add-card text="Competition" link="{{ route('comps.create') }}?type=org&org={{ $org->name }}"></x-add-card>


    </div>
    <br>
    {{ $comps->links() }}
    <br>
@endsection
