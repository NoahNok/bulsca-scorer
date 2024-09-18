@extends('layout')

@section('title')
    {{ $comp->name }} | Competitions | Admin
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.index') }}">Admin</a>
    </div>
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.comp.view', $comp) }}">{{ $comp->name }}</a>
    </div>
@endsection

@section('content')
    <h2>{{ $comp->name }}</h2>
    <small>
        <a href="{{ route('comps.view', $comp) }}" class="link">View</a>
    </small>
    <p>
        <strong>User email:</strong> {{ $comp->getUser?->email ?: 'N/A' }}
    </p>

    <br>

    @if ($comp->getUser)
        <form action="{{ route('admin.comp.update.userPassword', $comp) }}" method="post"
            onsubmit="return confirm('Are you sure you want to reset this accounts password?')">
            @csrf
            <button class="btn btn-danger">Reset account password</button>
        </form>
    @endif


    <br></br>


    <h3 class="mb-0">Update Competition</h3>
    <br>
    <form action="{{ route('admin.comp.update.post', $comp) }}" method="post">
        @csrf
        <div class="grid-4">
            <x-form-input id="name" title="Name" required placeholder="Uni Year (e.g. Warwick 2023)"
                defaultValue="{{ $comp->name }}"></x-form-input>
            <x-form-input id="when" title="When" required type="datetime-local"
                defaultValue="{{ $comp->when }}"></x-form-input>
            <x-form-input id="where" title="Where" required defaultValue="{{ $comp->where }}"></x-form-input>
            <div class="form-input ">
                <label for="isLeague" class="">League Competition</label>
                <select required id="isLeague" name="isLeague" class="input "
                    style="padding-top: 0.65em; padding-bottom: 0.75em;">

                    <option value="1" @if ($comp->isLeague == true) selected @endif>Yes</option>
                    <option value="0" @if ($comp->isLeague == false) selected @endif>No</option>

                </select>

            </div>
            <x-form-input id="lanes" title="Lanes" required type="number"
                defaultValue="{{ $comp->max_lanes }}"></x-form-input>
            <div class="form-input ">
                <label for="anytimepin" class="">Anytime Pin</label>
                <select required id="anytimepin" name="anytimepin" class="input "
                    style="padding-top: 0.65em; padding-bottom: 0.75em;">

                    <option value="1" @if ($comp->anytimepin == true) selected @endif>Yes</option>
                    <option value="0" @if ($comp->anytimepin == false) selected @endif>No</option>

                </select>

            </div>


            <x-form-select id="season" title="Season" :options="\App\Models\Season::all()"
                defaultValue="{{ $comp->season }}"></x-form-select>


            <x-form-select id="brand" title="Brand" :options="\App\Models\Brands\Brand::all()" defaultValue="{{ $comp->brand }}">
                <option value="none">No brand</option>
            </x-form-select>

            <div class="form-input ">
                <label for="scoring_type" class="">Scoring Type</label>
                <select required id="scoring_type" name="scoring_type" class="input "
                    style="padding-top: 0.65em; padding-bottom: 0.75em;">
                    @foreach (\App\Helpers\ScoringHelper::$availableTypes as $key => $data)
                        <option value="{{ $key }}" @if ($comp->scoring_type == $key) selected @endif>
                            {{ $data['name'] }}</option>
                    @endforeach




                </select>

            </div>

        </div>
        <button type="submit" class="btn">Save</button>
    </form>

    <br><br>
    <h3 class="mb-0">Delete Competition</h3>
    <br>
    <form action="{{ route('admin.comp.delete', $comp) }}"
        onsubmit="return confirm('This action cannot be undone! Are you sure?')" method="post">
        @csrf
        @method('DELETE')
        <div class="grid-4">
            <x-form-input id="compName" title="Name" required placeholder="{{ $comp->name }}"></x-form-input>
            <input type="hidden" name="compId" value="{{ $comp->id }}">
        </div>
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
@endsection
