@extends('layouts.admin')

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
    <h2 class="mb-0">Competitions</h2>
    <br>
    <div>
        @php
            $comps = \App\Models\Competition::orderBy('when', 'desc')->paginate(18);
        @endphp

        <div class="se-table se-table-thin">
            <table>

                <tbody>
                    @foreach ($comps as $comp)
                        <tr class="">
                            <td class="text-left font-semibold text-black relative">{{ $comp->name }} <a
                                    href="{{ route('comps.view', $comp) }}" class="absolute top-0 left-0 w-full h-full"></a>
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    <a href="{{ route('admin.comp.view', $comp) }}" class="link text-se!"><svg
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                        </svg>


                                    </a>
                                </div>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <x-add-card text="Competition" link="{{ route('admin.comp.create') }}"></x-add-card>


    </div>
    <br>
    {{ $comps->links() }}
    <br>

    <br><a href="{{ route('admin.records') }}" class="link">Edit Speed Event Record Times</a>
    <br><a href="{{ route('admin.brands') }}" class="link">Edit Brands</a>
    <br>
    <button class="btn" onclick="initSW()">Enable Notifications</button>
@endsection
