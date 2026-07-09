@extends('layouts.organisation')

@section('title')
    Create Championship
@endsection



@section('content')
    <h2>Create Championship</h2>
    <br>
    <form action="{{ route('orgs.championship.store', $org) }}" method="post">
        @csrf
        <div class="grid-4">

    
            
            <x-se-input name="name" type="text" label="Name" placeholder="Championship name" required />

            <x-se-input name="start_date" type="date" label="Start Date" placeholder="Championship start date" required />
            <x-se-input name="end_date" type="date" label="End Date" placeholder="Championship end date" required />


         

        </div>
        <br>

        <button type="submit" class="se-btn se-btn-success">Create</button>
    </form>
@endsection
