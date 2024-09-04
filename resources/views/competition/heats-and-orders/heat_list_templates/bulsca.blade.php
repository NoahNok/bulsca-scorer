      <div class="flex space-x-2  ">
          <div class=" hidden md:block  ">
              <h5>Lane</h5>
              <ol class="space-y-2">
                  @for ($l = 1; $l <= $comp->max_lanes; $l++)
                      <li class="px-5 py-3 border border-transparent">{{ $l }}</li>
                  @endfor
              </ol>
          </div>
          <div class=" w-full grid grid-cols-1 md:grid-cols-8 gap-3 ">


              @forelse ($heatEntries->sortBy(['heat','lane'])->groupBy('heat') as $key => $heat)
                  <div>
                      <h5>Heat {{ $key }}</h5>
                      <ol class=" list-item space-y-2">
                          @for ($l = 1; $l <= $comp->max_lanes; $l++)
                              @php
                                  $lane = $heat->where('lane', $l)->first();
                              @endphp

                              <li class="card  ">
                                  @if ($lane)
                                      <span
                                          class="whitespace-nowrap overflow-ellipsis overflow-hidden">{{ $lane->getTeam->formatName() }}</span>
                                  @else
                                      &nbsp;
                                  @endif
                              </li>
                          @endfor
                      </ol>
                  </div>
              @empty

                  <a href="{{ route('comps.view.heats.gen', $comp) }}"
                      class="btn flex items-center justify-center">Generate Heats</a>
              @endforelse

          </div>
      </div>
