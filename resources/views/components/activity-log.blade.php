<div class="flex-col space-y-3 mb-3">
    @foreach ($activities as $activity)
        <div class="se-card">
            <div class=" se-card-body">
                <div class=" grid grid-cols-1 md:grid-cols-[1fr_auto]  md:divide-x divide-gray-300 gap-2 md:gap-6">
                    <div>
                        <p class="font-semibold uppercase text-xs!">{{ $activity->created_at->format('H:i:s d/m/Y') }}
                        </p>

                        <h3>{{ $activity->activity }}</h3>
                        <p>{{ $activity->description ?? 'N/A' }}</p>
                    </div>
                    <div class="flex-col self-center items-center justify-center">
                        <p class="font-semibold uppercase text-xs!">Actioned by
                        </p>
                        <h4>{{ $activity->user->name ?? 'N/A' }}</h4>
                    </div>
                </div>
            </div>
            <div class="bg-gray-100 px-4 py-3 flex items-center divide-x divide-gray-300">
                @foreach ($activity->relations as $relation)
                    <div class="px-6 first-of-type:pl-0 last-of-type:pr-0">
                        {!! $relation->render() !!}
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

{!! $activities->links() !!}
