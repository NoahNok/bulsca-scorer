@extends('layout')

@section('title')
Results | {{ $comp->name }}
@endsection

@section('breadcrumbs')
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps') }}">Competitions</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view', $comp) }}">{{ $comp->name }}</a>
</div>
<div>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
    </svg>
    <a href="{{ route('comps.view.results', $comp) }}">Results</a>
</div>


@endsection

@section('content')

@if (!DigitalJudge::canGenerateCompetitionResults($comp))
<div class="alert-box">

    <h1>Unable to create results!</h1>
    <p><strong>You cannot generate results</strong> until Digitally Judged SERC's have had their marks confirmed by the Head Judge.
        <br>
        The following SERC's require confirming: @foreach (DigitalJudge::getSercsRequiringConfirmation($comp) as $serc)
        <a href="{{ route('comps.view.events.sercs.view', [$comp, $serc]) }}" class="link">{{ $serc->name }}</a>
        @endforeach
        <br>
        If you weren't expecting a SERC to be Digitally Judged, go to the SERC's settings and disable DigitalJudge,
    </p>

</div>
@endif


<h2>Results</h2>

<p><strong>Do not</strong> make a results sheet until you have made all your events! (You cannot edit which events are part of a results sheet after it has been made!)</p>
<br>
<p>The following result sheets are available:</p>
<br>

<div class="grid-4">
    @foreach ($comp->getResultSchemas as $schema)
    <a href="{{ route('comps.results.view-schema', ['comp' => $comp, 'schema' => $schema]) }}" class="p-5 border shadow-md bg-white rounded-md flex items-center justify-center space-x-2 hover:bg-gray-400 hover:text-white transition-colors cursor-pointer">
        <p class="text-lg font-semibold">{{ $schema->name }}</p>


    </a>
    @endforeach
    @if (DigitalJudge::canGenerateCompetitionResults($comp))
    <x-add-card link="{{ route('comps.view.results.add', $comp) }}" text="Results" />
    @endif
</div>
<br>
<h3>Quick Generate</h3>
<p class="mb-4">Click the button below to quickly generate the normal scoresheets for Overall, A-League and B-League</p>

@if (DigitalJudge::canGenerateCompetitionResults($comp))
<a href="{{ route('comps.view.results.quickGen', $comp) }}" class="btn">Quick Generate</a>
@endif

<br>
<br>
<hr>
<br>
<div class="grid-4">
    <div>
        <h3>Publicize Results</h3>
        @if (!$comp->isLeague)
        <div class="alert-box">

            <h1>Private Event</h1>
            <p>These results will <strong>not</strong> show on the public results page, and can only be accessed via the link below!
            </p>
        
        </div>
        @endif
        @if (!$comp->areResultsPublic())
        <p class="mb-4">Click the button below to make results publicly viewable</p>

        <a href="{{ route('comps.view.results.publishToggle', $comp) }}" class="btn">Publish Results</a>
        @else

        <p class="mb-2"><strong>Results link:</strong> <a href="{{ route('public.results.comp', $comp->resultsSlug())}}" class="link">Click to view public results</a>
            <br>Or scan the QR below
        </p>

        {!! QrCode::size(150)->style('round')->generate(route('public.results.comp', $comp->resultsSlug())) !!}

        <p class="mb-4 mt-6">

            Click the button below to hide results from being publicly viewable
        </p>


        <a href="{{ route('comps.view.results.publishToggle', $comp) }}" class="btn btn-danger">Unpublish Results</a>
        @endif
    </div>
    <div>
        <h3>Result Settings</h3>

        <div class="flex lg:flex-row justify-between lg:items-center lg:space-x-2 space-y-2 flex-col">
            <p><strong>Provisional</strong>: {{ $comp->areResultsProvisional() ? "Yes" : "No" }}
                <br>
                @if ($comp->areResultsProvisional())
                Making results final (not provisional) removes any notice about provisional results and allows CSV downloading, etc.
                @else
                Making results provisional adds a notice about provisional results and disables CSV downloading
                @endif
            </p>
            <div class="whitespace-nowrap">
                @if (!$comp->areResultsProvisional())
                <a href="{{ route('comps.view.results.provToggle', $comp) }}" class="btn">Make Provisional</a>
                @else
                <a href="{{ route('comps.view.results.provToggle', $comp) }}" class="btn">Make Final</a>
                @endif
            </div>
        </div>

    </div>
</div>



@endsection