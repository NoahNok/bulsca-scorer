@extends('digitaljudge.mpa-layout')

@section('title', 'DQ/Penalty')
@php
    $backlink = false;
    $icon =
        '<path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />';
@endphp

@section('content')
    @php
        $found = Session::get('activeSubmissions', []);
        $valid = \App\Models\DigitalJudge\JudgeDQSubmission::whereIn('id', $found)
            ->where('resolved', null)
            ->pluck('id')
            ->toArray();

    @endphp

    <div class="flex flex-col relative  " x-data="{ total: {{ json_encode($valid) }}, quickSubmitOpen: false }">


        <button class="btn" @click="() => $dispatch('startagain')">
            Submit new DQ/Penalty
        </button>

        <br>

        <template x-for="(frm,ind) in total">

            <form method="POST" @submit="handleFormSubmit" class="mb-20" x-show="!hideForm" x-collapse x-ref="form"
                x-data="{
                
                    submission: {
                        event: '',
                        heat_lane: '',
                        turn: '',
                        length: '',
                        code: '',
                        details: '',
                        name: '',
                        position: '',
                        seconder: { name: '', position: '' }
                    },
                    status: 'FORM',
                    showContent: true,
                    hideForm: false,
                    code: {
                        description: 'Please enter a DQ/Penalty code above',
                        cache: {}
                    },
                    resolveCode(code) {
                        code = code.toLowerCase();
                        if ((code.length < 3 && code[0] !== 'p') || (code.length < 2 && code[0] == 'p')) { this.code.description = 'Please enter a DQ/Penalty code above'; return; }
                        if (this.code.cache[code]) {
                            this.code.description = this.code.cache[code];
                            return;
                        }
                        fetch('{{ route('dj.dq.resolveCode', '') }}/' + code)
                            .then(response => response.json())
                            .then(data => {
                
                
                
                                this.code.description = data.description;
                
                                this.code.cache[code] = data.description;
                            })
                    },
                
                    handleFormSubmit(event) {
                        event.preventDefault();
                        this.status = 'WAITING';
                        this.$refs.form.querySelectorAll('input, select, textarea').forEach((el) => {
                            el.disabled = true
                        })
                
                        this.showContent = false;
                
                        let fd = new FormData();
                
                        for (var key in this.submission) {
                
                            if (key == 'seconder') {
                                for (var subkey in this.submission.seconder) {
                                    fd.append('seconder_' + subkey, this.submission.seconder[subkey]);
                                }
                                continue;
                            }
                
                            fd.append(key, this.submission[key]);
                
                        }
                
                        fetch('{{ route('dj.dq.submission') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: fd,
                
                            }).then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    this.startWaitingForResult(data.result);
                
                                }
                
                
                            }).catch((error) => {
                                showAlert('Something went wrong. Please try again.')
                                this.$refs.form.querySelectorAll('input, select, textarea ').forEach((el) => {
                                    el.disabled = false;
                                    this.showContent = true
                                })
                                this.status = 'FORM';
                            });
                
                        //this.status = Math.random() > 0.5 ? 'APPROVED' : 'REJECTED';
                    },
                
                    startWaitingForResult(id, updateNow = false) {
                
                        let doo = () => {
                            fetch('{{ route('dj.dq.submission.status', 'X') }}'.replace('X', id))
                                .then(response => response.json())
                                .then(data => {
                                    if (data.result != null) {
                
                                        clearInterval(interval);
                
                                        this.status = data.result == true ? 'APPROVED' : 'REJECTED';
                
                                        if (!data.result) {
                                            this.$refs.form.querySelectorAll('input, select, textarea ').forEach((el) => {
                                                el.disabled = false;
                
                                            })
                                            this.showContent = true
                                        }
                
                                    }
                
                
                                })
                        }
                
                        let interval = setInterval(doo,
                            3000)
                        if (updateNow) {
                            doo();
                        }
                
                    },
                
                    init() {
                        this.$refs.form.querySelectorAll('input, select, textarea').forEach((el) => {
                            el.addEventListener('invalid', () => {
                                el.parentNode.classList.add('is-invalid');
                            })
                
                            el.addEventListener('input', () => {
                
                                if (el.checkValidity()) {
                                    el.parentNode.classList.remove('is-invalid');
                                } else {
                                    el.parentNode.classList.add('is-invalid');
                                }
                
                            })
                
                            if (el.type == 'select-one') {
                                el.addEventListener('change', () => {
                                    if (el.value != '') {
                                        el.parentNode.classList.remove('is-invalid');
                                    } else {
                                        el.parentNode.classList.add('is-invalid');
                                    }
                                })
                            }
                
                
                
                        })
                
                        this.$refs.focus.focus();
                        this.$refs.focus.scrollIntoView({ behavior: 'smooth' });
                
                        if (frm != 0) {
                            this.status = 'WAITING'
                            this.showContent = false
                
                            fetch('{{ route('dj.dq.submission.info', 'X') }}'.replace('X', frm))
                                .then(response => response.json())
                                .then(data => {
                                    if (data.result != null) {
                
                
                                        let d = data.result;
                
                                        if (d.event_type.endsWith('SERC')) {
                                            d.event = `se:${d.event_id}`;
                                        } else {
                                            d.event = `sp:${d.event_id}`;
                                        }
                
                                        d.seconder = { name: d.seconder_name, position: d.seconder_position };
                
                                        this.submission = d;
                
                                        this.$refs.form.querySelectorAll('input, select, textarea ').forEach((el) => {
                                            el.disabled = true;
                
                                        })
                
                                        this.resolveCode(d.code);
                
                                    }
                
                
                
                                    this.startWaitingForResult(frm, true);
                                }).catch
                        }
                
                        if (frm == 0) {
                            let url = new URLSearchParams(window.location.search);
                            if (url.has('event')) {
                                this.submission.event = url.get('event');
                
                            }
                        }
                
                
                
                    }
                }">

                <div @click="showContent = !showContent" class="flex justify-between items-center">
                    <h2>Submission <span x-text="ind+1"></span></h2>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        :class="!showContent ? 'rotate-180' : ''" stroke="currentColor"
                        class="w-6 h-6 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>


                </div>

                <div x-show="showContent" x-collapse>

                    <h4>Select an Event</h4>

                    <div class="form-input ">
                        <label for="" class="">Event</label>
                        <select required name="event" x-ref="focus" x-model="submission.event" x-ref="event"
                            class="input " style="padding-top: 0.65em; padding-bottom: 0.65em;">
                            <option value="">Please select an event...</option>


                            <optgroup label="Speeds">
                                @foreach ($comp->getSpeedEvents as $speed)
                                    <option value="sp:{{ $speed->id }}">
                                        {{ $speed->getName() }}</option>
                                @endforeach
                            </optgroup>

                            <optgroup label="SERCs">
                                @foreach ($comp->getSercs as $serc)
                                    <option value="se:{{ $serc->id }}">
                                        {{ $serc->getName() }}</option>
                                @endforeach
                            </optgroup>
                        </select>

                    </div>

                    <div class="form-input ">
                        <label for="" class="">Heat & Lane/Team</label>
                        <select name="heat-lane" required x-model="submission.heat_lane" x-ref="event" class="input "
                            style="padding-top: 0.65em; padding-bottom: 0.65em;">
                            <option value="">Please select a heat and lane</option>

                            @foreach ($comp->getHeatEntries->sortBy('heat')->groupBy('heat') as $heat)
                                <optgroup label="Heat {{ $heat[0]->heat }}">
                                    @foreach ($heat->sortBy('lane') as $lane)
                                        <option value="{{ $lane->id }}">
                                            {{ $lane->heat }}-{{ $lane->lane }}:
                                            {{ $lane->getTeam->getFullname() }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach



                        </select>



                    </div>

                    <div class="grid-2">
                        <div class="form-input ">
                            <label for="" class="">Turn #</label>
                            <input type="number" name="turn" x-model="submission.turn">

                        </div>
                        <div class="form-input ">
                            <label for="" class="">Length #</label>
                            <input type="number" name="length" x-model="submission.length">

                        </div>

                    </div>

                    <div>
                        <h4>DQ/Penalty Info</h4>



                        <div class="form-input " style="margin-bottom: 0px !important">
                            <input type="text" name="code" required x-ref="code" x-model="submission.code"
                                placeholder="DQXXX or PXXX" @keyup="resolveCode($event.target.value)"
                                x-mask:dynamic="$input.toUpperCase().startsWith('P') ? 'P999' : 'DQ999'">

                        </div>

                        <div name="" id="" readonly class="w-full  -mt-3 text-sm text-gray-500 mb-6"
                            x-text="code.description">
                            Manual
                            DQ/P description fills here
                        </div>

                        <label for="">Aditional Details</label>
                        <textarea name="" id=""
                            class="w-full border hover:border-gray-400 p-3 h-max focus:border-gray-400 outline-none rounded-md"
                            placeholder="..." x-model="submission.details"></textarea>

                    </div>

                    <div class="grid-2 mt-2">
                        <div class="form-input " style="margin-bottom: 0px !important">
                            <label for="">Your Name</label>
                            <input type="text" name="name" required x-model="submission.name" placeholder="Name">

                        </div>
                        <div class="form-input " style="margin-bottom: 0px !important">
                            <label for="">Your Position</label>
                            <input type="text" name="position" required x-model="submission.position"
                                placeholder="Position">

                        </div>
                        <div class="form-input -mt-4" style="margin-bottom: 0px !important">
                            <label for="">Seconder Name</label>
                            <input type="text" name="seconderName" x-model="submission.seconder.name"
                                placeholder="Name">

                        </div>
                        <div class="form-input -mt-4 " style="margin-bottom: 0px !important">
                            <label for="">Seconder Position</label>
                            <input type="text" name="seconderPosition" x-model="submission.seconder.position"
                                placeholder="Position">

                        </div>
                    </div>
                </div>
                <br>
                <div>
                    <div x-show="status=='WAITING'" style="display: none"><x-loader />
                        <p class="text-center">Waiting for approval...</p>
                    </div>


                    <div x-show="status=='APPROVED'"
                        class="flex flex-col items-center justify-center p-3 bg-green-500 rounded-md shadow-md font-semibold text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>

                        Approved
                    </div>

                    <div x-show="status=='REJECTED'"
                        class="flex flex-col items-center justify-center p-3 bg-red-500 rounded-md shadow-md font-semibold text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>


                        Rejected
                    </div>


                    <button x-show="status=='FORM'" class="btn w-full">Submit</button>
                    <button x-show="status=='REJECTED'" class="btn w-full mt-2">Re-submit</button>
                    <button type="button" x-show="status=='REJECTED'" @click="hideForm = true"
                        class="btn w-full btn-thin btn-danger mt-2">Or
                        clear</button>
                </div>









            </form>
        </template>




        <div class="absolute top-0 left-0 w-full h-full bg-white" x-show="quickSubmitOpen" x-cloak
            x-data="{
                activeStep: 1,
            
                events: {{ json_encode($comp->getEventsInDQFormat()) }},
            
                presetEvent: '',
            
            
            
                codes: [],
                loadingCodes: false,
            
                codeSearch: '',
            
                loadCodes() {
                    this.loadingCodes = true;
                    fetch('{{ route('dj.dq.event-codes', 'X') }}'.replace('X', this.event))
                        .then(response => response.json())
                        .then(data => {
                            this.codes = data;
                            this.loadingCodes = false
                        })
            
                },
            
            
            
                get eventName() {
                    if (this.submission.event == '') return '';
                    return this.events[this.submission.event]
                },
                teamName: '',
            
            
                codeDescription: '',
            
                submission: {
                    event: '',
                    heat_lane: '',
                    turn: '',
                    length: '',
                    code: '',
                    details: '',
                    name: '',
                    position: '',
                    seconder: { name: '', position: '' }
                },
            
            
            
            
            
                setEvent(event) {
                    this.submission.event = event;
            
                    this.activeStep = 2;
                },
            
                setTeam(lane, name) {
                    this.submission.heat_lane = lane;
                    this.teamName = name;
                    this.activeStep = 3;
                    this.loadCodes();
                },
            
                setCode(code, description) {
                    this.submission.code = code;
                    this.codeDescription = description;
                    this.activeStep = 4;
                },
            
                startAgain() {
            
            
                    this.submission = {
                        event: '',
                        heat_lane: '',
                        turn: '',
                        length: '',
                        code: '',
                        details: '',
                        name: '',
                        position: '',
                        seconder: { name: '', position: '' }
                    }
            
            
                    this.submission.name = '{{ $judge_name }}';
            
            
            
            
                    if (this.presetEvent != '') {
                        this.submission.event = this.presetEvent;
                        this.activeStep = 2;
                        return;
                    } else {
                        this.activeStep = 1;
                    }
            
                    this.quickSubmitOpen = true;
            
            
            
            
            
                },
            
                shouldDisplaySelf(code, codePad, description) {
            
            
                    let search = this.codeSearch.toLowerCase().trim()
            
                    if (search == '') return true;
            
                    return code.startsWith(search) || codePad.startsWith(search) || description.toLowerCase().includes(search);
            
            
                },
            
                shouldDisplayGroup(type, codes) {
            
                    for (let code of codes) {
                        if (this.shouldDisplaySelf(type + code.id, type + code.id.toString().padStart(3, '0'), code.description)) {
                            return true;
                        }
                    }
            
                    return false
            
                },
            
                shouldDisplaySection(type) {
                    let search = this.codeSearch.toLowerCase().trim()
            
                    if (search == '') return true;
            
                    return type.startsWith(search)
                },
            
                handleFormSubmit(event) {
                    event.preventDefault();
            
            
            
            
                    let fd = new FormData();
            
                    for (var key in this.submission) {
            
                        if (key == 'seconder') {
                            for (var subkey in this.submission.seconder) {
                                fd.append('seconder_' + subkey, this.submission.seconder[subkey]);
                            }
                            continue;
                        }
            
                        fd.append(key, this.submission[key]);
            
                    }
            
                    fetch('{{ route('dj.dq.submission') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: fd,
            
                        }).then(response => response.json())
                        .then(data => {
                            this.total.push(data.result);
                            this.quickSubmitOpen = false;
            
            
                        }).catch((error) => {
                            showAlert('Something went wrong. Please try again.')
            
                        });
            
                    //this.status = Math.random() > 0.5 ? 'APPROVED' : 'REJECTED';
                },
            
            
            }" x-init="() => {
                let url = new URLSearchParams(window.location.search);
                if (url.has('event')) {
                    presetEvent = url.get('event');
            
            
                }
            
            
            }" @startagain.window="() => startAgain()">


            <span class="link" @click="quickSubmitOpen = false">Cancel Submission</span>

            <div x-show="activeStep == 1">
                <h2 class="text-2xl font-semibold">Select an Event</h2>


                <div class="flex flex-col space-y-3">
                    <h5>Speeds</h5>
                    @foreach ($comp->getSpeedEvents as $speed)
                        <button class="btn btn-primary" @click="setEvent('sp:{{ $speed->id }}')">
                            {{ $speed->getName() }}</button>
                    @endforeach

                    <h5>SERCs</h5>
                    @foreach ($comp->getSercs as $serc)
                        <button class="btn btn-primary" @click="setEvent('se:{{ $serc->id }}')">
                            {{ $serc->getName() }}</button>
                    @endforeach
                </div>
            </div>


            <div x-show="activeStep == 2">

                <p>Event: <span x-text="eventName" class="link cursor-pointer" @click="activeStep = 1"></span></p>

                <h2 class="text-2xl font-semibold mt-2">Select a
                    team</h2>


                <div class="flex flex-col space-y-3">
                    @foreach ($comp->getHeatEntries->sortBy('heat')->groupBy('heat') as $heat)
                        <h4>Heat {{ $heat[0]->heat }}</h4>

                        @foreach ($heat->sortBy('lane') as $lane)
                            <button class="btn btn-primary" style="text-align: left"
                                @click="setTeam({{ $lane->id }}, 'Heat {{ $lane->heat }}, Lane {{ $lane->lane }}, {{ $lane->getTeam->getFullname() }}')">
                                Lane {{ $lane->lane }}:
                                {{ $lane->getTeam->getFullname() }}
                            </button>
                        @endforeach
                    @endforeach
                </div>

                <br>
                <br>
                <br>
            </div>

            <div x-show="activeStep == 3">

                <p>Event: <span x-text="eventName" class="link cursor-pointer" @click="activeStep = 1"></span></p>
                <p>Team: <span x-text="teamName" class="link cursor-pointer" @click="activeStep = 2"></span></p>

                <h2 class="text-2xl font-semibold mt-2">Select a DQ/Penalty</h2>

                <div class="form-input" style="margin-bottom: 0 !important">
                    <input type="text" placeholder="Search..." style="margin-bottom: 0 !important"
                        x-model="codeSearch">
                </div>

                <div class="flex flex-col items-center justify-center mt-2" x-show="loadingCodes">
                    <x-loader />
                    <small>Loading codes...</small>
                </div>


                <div class="flex flex-col space-y-3 mt-2" x-show="!loadingCodes" x-data="{
                    dqOpen: true,
                    penOpen: true
                }">
                    <div class="flex justify-between cursor-pointer" @click="dqOpen = !dqOpen"
                        x-show="shouldDisplaySection('dq')">
                        <h4 class="hmb-0">DQs</h4>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 transition-transform ease-in-out"
                            :class="!dqOpen ? 'rotate-180' : ''">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                        </svg>

                    </div>
                    <div x-collapse x-show="dqOpen">
                        <template x-for="(dqs, groupName) in codes?.related?.dq">
                            <div class="mb-5 last:mb-0" x-show="shouldDisplayGroup('dq', dqs)">
                                <h5 x-text="groupName"></h5>

                                <div class="flex flex-col space-y-2">
                                    <template x-for="dq in dqs">
                                        <div class="card cursor-pointer"
                                            @click="setCode(`DQ${dq.id.toString().padStart(3, '0')}`, dq.description)"
                                            x-show="shouldDisplaySelf(`dq${dq.id}`, `dq${dq.id.toString().padStart(3, '0')}`, dq.description)">
                                            <strong>DQ<span x-text="dq.id.toString().padStart(3, '0')"></span></strong>
                                            <p x-text="dq.description"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </template>

                        <template x-for="(dqs, groupName) in codes?.other?.dq">
                            <div class="mb-5 last:mb-0" x-show="shouldDisplayGroup('dq', dqs)">
                                <h5 x-text="groupName"></h5>

                                <div class="flex flex-col space-y-2">
                                    <template x-for="dq in dqs">
                                        <div class="card cursor-pointer"
                                            @click="setCode(`DQ${dq.id.toString().padStart(3, '0')}`, dq.description)"
                                            x-show="shouldDisplaySelf(`dq${dq.id}`, `dq${dq.id.toString().padStart(3, '0')}`, dq.description)">
                                            <strong>DQ<span x-text="dq.id.toString().padStart(3, '0')"></span></strong>
                                            <p x-text="dq.description"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </template>

                        <br x-show="shouldDisplaySection('dq')">
                    </div>



                    <div class="flex justify-between cursor-pointer" @click="penOpen = !penOpen"
                        x-show="shouldDisplaySection('p')">
                        <h4 class="hmb-0">Penalties</h4>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 transition-transform ease-in-out"
                            :class="!penOpen ? 'rotate-180' : ''">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                        </svg>

                    </div>
                    <div x-collapse x-show="penOpen">
                        <template x-for="(pens, groupName) in codes?.related?.pen">
                            <div class="mb-5 last:mb-0" x-show="shouldDisplayGroup('p', pens)">
                                <h5 x-text="groupName"></h5>

                                <div class="flex flex-col space-y-2">
                                    <template x-for="pen in pens">
                                        <div class="card cursor-pointer"
                                            @click="setCode(`P${pen.id.toString().padStart(3, '0')}`, pen.description)"
                                            x-show="shouldDisplaySelf(`p${pen.id}`, `p${pen.id.toString().padStart(3, '0')}`, pen.description)">
                                            <strong>P<span x-text="pen.id.toString().padStart(3, '0')"></span></strong>
                                            <p x-text="pen.description"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </template>


                        <template x-for="(pens, groupName) in codes?.other?.pen">
                            <div class="mb-5 last:mb-0" x-show="shouldDisplayGroup('p', pens)">
                                <h5 x-text="groupName"></h5>

                                <div class="flex flex-col space-y-2">
                                    <template x-for="pen in pens">
                                        <div class="card cursor-pointer"
                                            @click="setCode(`P${pen.id.toString().padStart(3, '0')}`, pen.description)"
                                            x-show="shouldDisplaySelf(`p${pen.id}`, `p${pen.id.toString().padStart(3, '0')}`, pen.description)">
                                            <strong>P<span x-text="pen.id.toString().padStart(3, '0')"></span></strong>
                                            <p x-text="pen.description"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>

                        </template>
                    </div>

                    <br>
                    <br>
                    <br>

                </div>
            </div>


            <form @submit="handleFormSubmit" x-show="activeStep == 4">
                <p>Event: <span x-text="eventName" class="link cursor-pointer" @click="activeStep = 1"></span></p>
                <p>Team: <span x-text="teamName" class="link cursor-pointer" @click="activeStep = 2"></span></p>

                <div class="flex items-center space-x-3">
                    <h3 x-text="submission.code" class="mt-2"></h3>
                    <span class="link cursor-pointer" @click="activeStep = 3">Change</span>
                </div>

                <p x-text="codeDescription"></p>
                <br>

                <div class="grid-2" x-show="submission.event.startsWith('sp')">
                    <div class="form-input ">
                        <label for="" class="">Turn #</label>
                        <input type="number" name="turn" x-model="submission.turn">

                    </div>
                    <div class="form-input ">
                        <label for="" class="">Length #</label>
                        <input type="number" name="length" x-model="submission.length">

                    </div>

                </div>

                <label for="">Aditional Details</label>
                <textarea name="" id=""
                    class="w-full border hover:border-gray-400 p-3 h-max focus:border-gray-400 outline-none rounded-md"
                    placeholder="..." x-model="submission.details"></textarea>


                <div class="grid-2 mt-2">
                    <div class="form-input " style="margin-bottom: 0px !important">
                        <label for="">Your Name <span class="ml-auto text-bulsca_red">*</span></label>
                        <input type="text" name="name" required x-model="submission.name" placeholder="Name">

                    </div>
                    <div class="form-input " style="margin-bottom: 0px !important">
                        <label for="">Your Position<span class="ml-auto text-bulsca_red">*</span></label>
                        <input type="text" name="position" required x-model="submission.position"
                            placeholder="Position">

                    </div>
                    <div class="form-input -mt-4" style="margin-bottom: 0px !important">
                        <label for="">Seconder Name</label>
                        <input type="text" name="seconderName" x-model="submission.seconder.name" placeholder="Name">

                    </div>
                    <div class="form-input -mt-4 " style="margin-bottom: 0px !important">
                        <label for="">Seconder Position</label>
                        <input type="text" name="seconderPosition" x-model="submission.seconder.position"
                            placeholder="Position">

                    </div>
                </div>
                <br>
                <button class="btn w-full">Submit</button>


            </form>



        </div>






    </div>

@endsection
