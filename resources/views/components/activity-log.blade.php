<div class="flex-col space-y-3 mb-3">

    @if (count($pickers) > 0)
        <div class="flex flex-col md:flex-row md:flex-wrap gap-2" x-data="{
            addFilter(param, value) {
                if (!value) return;

                const url = new URL(window.location.href);
                const values = (url.searchParams.get(param) ?? '').split('|').filter(v => v !== '');

                if (!values.includes(value)) values.push(value);

                url.searchParams.set(param, values.join('|'));
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }
        }">
            @foreach ($pickers as $picker)
                <select aria-label="Filter by {{ strtolower($picker['label']) }}"
                    @change="addFilter('{{ $picker['param'] }}', $el.value)"
                    class="border-2 rounded-md py-2 px-2 bg-white">
                    <option value="" selected>{{ $picker['placeholder'] }}</option>
                    @foreach ($picker['groups'] as $group => $options)
                        @if ($group === '')
                            @foreach ($options as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                        @elseif (count($options) > 0)
                            <optgroup label="{{ $group }}">
                                @foreach ($options as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
            @endforeach
        </div>
    @endif

    @if (count($chipGroups) > 0)
        <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
            <p class="font-semibold uppercase text-xs!">Showing activity for</p>
            @foreach ($chipGroups as $group)
                @if (!$loop->first)
                    <span class="text-xs text-gray-500">and</span>
                @endif
                <div class="flex flex-wrap items-center gap-1">
                    <span class="text-xs font-semibold text-gray-500">{{ $group['label'] }}</span>
                    @foreach ($group['chips'] as $chip)
                        @if (!$loop->first)
                            <span class="text-xs text-gray-500">or</span>
                        @endif
                        <span class="badge badge-info inline-flex items-center gap-1">
                            {{ $chip['label'] }}
                            <a href="{{ $chip['remove_url'] }}" class="hover:opacity-75"
                                aria-label="Remove filter {{ $group['label'] }}: {{ $chip['label'] }}">&times;</a>
                        </span>
                    @endforeach
                </div>
            @endforeach
            <a href="{{ $clearUrl }}" class="link text-sm">Clear</a>
        </div>
    @endif

    <div x-data="{ open: false }">
        <button type="button" class="flex items-center w-full my-3 mb-3! hover:text-se" @click="open = !open"
            :aria-expanded="open">
            <h3>Activity types</h3>
            <hr class="spacer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6 transition-transform ml-2 rotate-180" :class="open ? '' : 'rotate-0!'">
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
        </button>

        <div x-show="open" x-collapse class="flex flex-wrap gap-1">
            @foreach ($types as $type)
                <a href="{{ $type['url'] }}" @class(['se-btn text-xs! font-semibold!', 'se-btn-primary' => $type['active']])
                    aria-pressed="{{ $type['active'] ? 'true' : 'false' }}">{{ $type['label'] }}</a>
            @endforeach
        </div>
    </div>

    <br>


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
                    @php(['filter' => $filter, 'link' => $link] = $footerLinks($relation))
                    <div class="px-6 first-of-type:pl-0 last-of-type:pr-0 flex items-center gap-1">
                        @if ($filter)
                            <a href="{{ $filter['url'] }}" aria-pressed="{{ $filter['active'] ? 'true' : 'false' }}"
                                title="{{ $filter['active'] ? 'Stop filtering by this' : 'Filter by this' }}"
                                @class(['rounded-md px-1 -mx-1 hover:bg-gray-200', 'bg-se/10 ring-1 ring-se' => $filter['active']])>
                                {!! $relation->render($activity) !!}
                            </a>
                        @else
                            {!! $relation->render($activity) !!}
                        @endif
                        @if ($link)
                            <a href="{{ $link }}" class="text-gray-500 hover:text-se" title="Open page"
                                aria-label="Open page">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-4" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <p>No activities found.</p>
    @endforelse




</div>

{!! $activities->withQueryString()->links() !!}
