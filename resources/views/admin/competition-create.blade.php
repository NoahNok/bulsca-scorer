@extends('layouts.admin')

@section('title')
    Create Competition
@endsection

@section('breadcrumbs')
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.index') }}">Admin</a>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-3 h-3">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
        <a href="{{ route('admin.comp.create') }}">Create Competition</a>
    </div>
@endsection

@section('content')
    <h2 class="mb-0">Create Competition</h2>
    <br>
    <form action="{{ route('admin.comp.create.post') }}" method="post">
        @csrf
        <div class="grid-4">


            <div class="se-form-input">
                <label for="name">Name</label>
                <input type="text" name="name" id="name" placeholder="Name" value="{{ old('name') }}" required>
            </div>

            <div class="se-form-input">
                <label for="when">Date</label>
                <input type="datetime-local" name="when" id="when" placeholder="When" value="{{ old('when') }}"
                    required>
            </div>

            <div class="se-form-input">
                <label for="where">Location</label>
                <input type="text" name="where" id="where" placeholder="Where" value="{{ old('where') }}"
                    required>
            </div>

            <div class="se-form-input ">
                <label for="isLeague">League</label>
                <select required id="isLeague" name="isLeague">

                    <option value="1">Yes</option>
                    <option value="0" @if (request()->get('isLeague') == 'false') selected @endif>No</option>

                </select>

            </div>

            <div class="se-form-input">
                <label for="lanes">Lane</label>
                <input type="number" name="lanes" id="lanes" placeholder="Lanes" value="{{ old('lanes') }}"
                    required>
            </div>


            <div class="se-form-input ">
                <label for="anytimepin">Any-time Pin</label>
                <select required id="anytimepin" name="anytimepin">
                    <option value="0">No</option>
                    <option value="1">Yes</option>


                </select>

            </div>


            <div class="se-form-input ">
                <label for="scoring_type" class="">Scoring Type</label>
                <select required id="scoring_type" name="scoring_type" class="input ">
                    @foreach (\App\Helpers\ScoringHelper::$availableTypes as $key => $data)
                        <option value="{{ $key }}">{{ $data['name'] }}</option>
                    @endforeach




                </select>

            </div>
        </div>
        <button type="submit" class="se-btn se-btn-success">Add</button>
    </form>
@endsection
