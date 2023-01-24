@extends('layout')

@section('title')
Speed Records | Admin
@endsection

@section('breadcrumbs')
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('admin.index') }}">Admin</a>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('admin.records') }}">Speed Records</a>
</div>

@endsection

@section('content')
<div class="grid-2">
    <div class="flex flex-col space-y-4">

        <div class="flex justify-between">
            <h2 class="mb-0">Speed Event Records</h2>
            <button table-submit="records" class="btn">Save</button>
        </div>




        <div class="  relative w-full  ">
            <table editable-table="records" table-submit-csrf="{{ csrf_token() }}" table-after-url="{{ route('admin.records') }}" table-submit-url="{{ route('admin.records.update') }}" class=" editable-table text-sm w-full shadow-md rounded-lg overflow-hidden text-left text-gray-500 ">
                <thead class="text-xs text-gray-700 text-right uppercase bg-gray-50 ">
                    <tr>
                        <th scope="col" class="py-3 px-6 text-left">
                            Event
                        </th>

                        <th scope="col" class="py-3 px-6">
                            Record
                        </th>



                    </tr>
                </thead>
                <tbody>

                    @forelse ($events as $event)
                    <tr table-row table-row-owner="{{ $event->id }}" class="bg-white border-b text-right ">
                        <th scope="row" class="py-4 text-left px-6 font-medium text-gray-900 whitespace-nowrap ">
                            {{ $event->name }}
                        </th>
                        @php
                        $mins = floor($event->record / 60000);
                        $secs = (($event->record)-($mins*60000))/1000;
                        @endphp

                        <td>
                            <input class="table-input" table-cell table-cell-name="record" placeholder="00:00.000" type="text" value="{{ $event->record != null ? sprintf("%02d", $mins) . ':' . str_pad(number_format($secs, 3, '.', ''), 6, '0', STR_PAD_LEFT) : '' }}">
                        </td>



                    </tr>
                    @empty
                    <tr class="bg-white border-b text-right ">
                        <th colspan="100" scope="row" class="py-4 text-left px-6 text-center font-medium text-gray-900 whitespace-nowrap ">
                            None
                        </th>
                    </tr>
                    @endforelse



                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection