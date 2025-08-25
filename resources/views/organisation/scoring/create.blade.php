@extends('layouts.organisation')



@section('content')
    <h2 class="mb-0">Create Scoring Schema</h2>
    <p>Create and modify event scoring profiles for this organisation.</p>

    <x-scoring-schema-editor type="org" route="{{ route('orgs.scoring.create.post', $org->name) }}" />
@endsection
