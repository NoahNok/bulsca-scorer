<div class="flex-col space-y-3 mb-3">

    <div x-data="{
        filters: {
            activity: '{{ request()->query('activity', '') }}'.split('|'),
        },
    
        toggleActivity(value) {
            if (this.filters.activity.includes(value)) {
                this.filters.activity = this.filters.activity.filter(v => v !== value);
            } else {
                this.filters.activity = [...this.filters.activity, value];
            }
        },
    
        applyFilters() {
            const query = new URLSearchParams();
    
            if (this.filters.activity.length > 0) {
                query.set('activity', this.filters.activity.join('|'));
            }
    
            window.location.search = query.toString();
        }
    }">
        <h3>Filters</h3>

        <div>
            <h4>Activity</h4>
            <div>
                @foreach ($types as $type)
                    <button class="se-btn text-xs! font-semibold! "
                        :class="{ 'bg-se text-white hover:bg-se!': filters.activity.includes('{{ $type }}') }"
                        @click="toggleActivity('{{ $type }}')">{{ str_replace('_', ' ', $type) }}</button>
                @endforeach
            </div>

            <button class="se-btn mt-2" @click="applyFilters()">Apply Filters</button>
        </div>
    </div>


    @forelse ($activities as $activity)
        <div class="se-card">
            <div class=" se-card-body">
                <div class=" grid grid-cols-1 md:grid-cols-[1fr_auto]  md:divide-x divide-gray-300 gap-2 md:gap-6">
                    <div>
                        <p class="font-semibold uppercase text-xs!">{{ $activity->created_at->format('H:i:s d/m/Y') }}
                        </p>

                        <h3 class=" capitalize!">{{ strtolower(str_replace('_', ' ', $activity->activity)) }}</h3>
                        <p>{{ $activity->description ?? 'N/A' }}</p>


                        {!! $activity->renderContext() !!}

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
                        {!! $relation->render($activity) !!}
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p>No activities found.</p>
    @endforelse




</div>

{!! $activities->links() !!}
