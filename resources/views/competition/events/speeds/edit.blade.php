@extends('layouts.competition')

@section('title')
    Edit | {{ $event->getName() }}
@endsection



@section('content')
    <h2>{{ $event->getName() }}</h2>
    <br>
    <form action="{{ route('comps.events.speeds.editPost', [$comp, $event]) }}" method="post">
        @csrf
        <div class="grid-4">




            <div>
                <p class="text-red-500 font-semibold mb-1">Changing the target type will delete all current results!</p>
                <div class="se-form-input" style="margin-bottom: 0 !important">
                    <label for="">Target</label>

                    <select style="margin-bottom: 0 !important" name="target_entity"id="target_select" required>
                        <option value="club" @if ($event->scorable_entity == 'club') selected @endif>Clubs</option>
                        <option value="team" @if ($event->scorable_entity == 'team') selected @endif>Teams</option>
                        <option value="competitor" @if ($event->scorable_entity == 'competitor') selected @endif>Competitiors</option>
                    </select>

                    <script>
                        const select = document.getElementById('target_select');
                        let previousValue = select.value;

                        select.addEventListener('change', function(e) {
                            const confirmed = confirm(
                                'Are you sure you want to change the target type? ALL results will be removed when you click save.'
                            );
                            if (!confirmed) {
                                // Revert to previous value
                                select.value = previousValue;
                            } else {
                                // Update previous value
                                previousValue = select.value;
                            }
                        });
                    </script>
                </div>

            </div>


            <div class="se-checkbox">
                <div>
                    <input type="checkbox" id="has_penalties" name="has_penalties"
                        @if ($event->has_penalties) checked @endif>
                    <label for="has_penalties">Has Penalties</label>
                </div>
                <p>Enables/Disabled the ability to enter and show penalties for this event.</p>
            </div>

            <input type="number" name="weight" value="1" required class="hidden">
            <input type="text" name="record" value="00:00.000" required class="hidden">


        </div>
        <br>

        <button type="submit" class="se-btn se-btn-success">Save</button>
    </form>
@endsection
