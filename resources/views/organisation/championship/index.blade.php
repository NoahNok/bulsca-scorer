@extends('layouts.organisation')



@section('content')
    <h2 class="mb-0">Championships</h2>
    <br>
    <div>
       

        <div class="se-table se-table-thin">
            <table>

                <tbody>
                    @foreach ($championships as $championship)
                        <tr class="">
                            <td class="text-left font-semibold text-black relative">{{ $championship->name }} <a href="{{ route('orgs.championship.view', ['organisation' => $org->name, 'championship' => $championship]) }}"
                                    class="absolute top-0 left-0 w-full h-full"></a>
                            </td>

                            <td class=" flex items-center justify-end gap-6">
                                <div class="flex items-center  gap-3 tooltip-left " title="Competitions">
                              
                                    <div class="flex items-center justify-center gap-1" >
                                        {{ $championship->competitions->count() }} <svg xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                            class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                        </svg>
                                    </div>
                                   
                                </div>
                                <div>{{ $championship->start_date->format('d/m/Y') }} - {{ $championship->end_date->format('d/m/Y') }}</div>

                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <x-add-card text="Championship" link="{{ route('orgs.championship.create', $org) }}"></x-add-card>


    </div>
    <br>
    {{ $championships->links() }}
    <br>
@endsection
