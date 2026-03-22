@extends('layouts.organisation')



@section('content')
    <h2 class="mb-0">{{ $schema->name }}</h2>
    <p>Create and modify event scoring profiles for this organisation.</p>

    <x-scoring-schema-editor type="org" :schema="$schema" :org="$org"
        route="{{ route('orgs.scoring.edit.post', [$org->name, $schema->id]) }}"
        delete_route="{{ route('orgs.scoring.delete', [$org->name, $schema->id]) }}" edit_create="true" />
@endsection
