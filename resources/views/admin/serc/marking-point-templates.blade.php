@extends('layouts.admin')

@section('title')
    Marking Point Templates
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
    <h2 class="mb-0">Marking Point Templates</h2>
    <br>
    <div class="se-table se-table-thin mb-2">
        <table>

            <tbody>
                @foreach ($templates as $template)
                    <tr class="relative">
                        <th class="relative">{{ $template->name }} <a
                                href="{{ route('admin.serc.marking-point-template.edit', $template) }}"
                                class="absolute inset-0  w-full h-full"></a></th>
                        <td>{{ ucfirst($template->settings['mode']) }}</td>



                    </tr>
                @endforeach
            </tbody>
        </table>
        <x-add-card text="Competition" link="{{ route('admin.serc.marking-point-template.create') }}"></x-add-card>
    </div>
@endsection
