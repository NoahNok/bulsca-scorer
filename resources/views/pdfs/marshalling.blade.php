@extends('pdfs.layout')

@section('cover')
    <ul class="list-disc">
        @foreach ($sections as $section)
            <li>{{ $section['title'] }}</li>
        @endforeach
    </ul>
@endsection

@section('content')
    @foreach ($sections as $section)
        <div class="pack-section">
            <h2 class="mb-4">Marshalling - {{ $section['title'] }}</h2>

            @foreach ($section['blocks'] as $block)
                <div class="pack-block pb-4">
                    <div class="flex items-center justify-between border-b border-black mb-1">
                        <h4>{{ $block['name'] }}</h4>
                        <p class="text-sm">{{ $block['leagues'] }}</p>
                    </div>
                    <ol class="space-y-1">
                        @foreach ($block['lines'] as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ol>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
