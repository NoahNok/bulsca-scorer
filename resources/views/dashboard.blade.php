@extends('layouts.guest')

@section('title', 'Dashboard')

@section('content')
    <div class="se-table se-table-thin">
        <table>

            <tbody>
                @foreach ($comps as $comp)
                    <tr class="">
                        <td class="text-left font-semibold text-black relative">{{ $comp->name }} <a
                                href="{{ route('comps.view', $comp) }}" class="absolute top-0 left-0 w-full h-full"></a>
                        </td>

                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
