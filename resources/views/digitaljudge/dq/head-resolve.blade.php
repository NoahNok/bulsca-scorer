@extends('digitaljudge.mpa-layout')

@section('title', 'DQ/Penalty')
@php
    $backlink = false;
    $icon =
        '<path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />';
@endphp

@section('content')


    <div class="flex flex-col  " x-data="{
        found: [],
        approved: [],
    
    
        fetchData() {
            fetch('{{ route('dj.dq.resolve.list') }}').then(response => response.json()).then(data => {
    
                let formatted = []
    
                data.result.forEach(d => {
    
    
                    if (d.event_type.endsWith('SERC')) {
                        d.event = `se:${d.event_id}`;
                    } else {
                        d.event = `sp:${d.event_id}`;
                    }
    
                    d.seconder = { name: d.seconder_name, position: d.seconder_position };
                })
    
                // Lets find the difference and only get new entries
                let newEntries = data.result.filter(d => !this.found.find(f => f.id == d.id));
    
                // This still contains old entires that are complete. They are just hidden - this fixes the buggy behaviour
                this.found.push.apply(this.found, newEntries);
            })
    
            fetch('{{ route('dj.dq.accepted') }}').then(resp => resp.json()).then(data => {
                this.approved = data
            })
        },
    
        showLoader() {
            console.log(this.found.length)
            let ret = true
            this.found.forEach(f => {
                if (!f.complete) { ret = ret && false }
            })
            return ret;
        },
    
    
        init() {
    
    
            this.fetchData();
    
            setInterval(() => {
                this.fetchData();
            }, 3000);
    
    
    
        },
    
        clearApproved() {
            this.approved = []
            sessionStorage.setItem('approved', JSON.stringify(this.approved));
    
        }
    }">

        <template x-for="submission in found" x-key="submission.id">

            <div class="mb-20 relative" x-ref="form" x-show="!complete" x-collapse x-data="{
            
            
                complete: false,
                showContent: true,
                resolved: null,
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
            
                makeDecision(resolved) {
            
                    if (this.complete) return;
            
                    this.complete = true;
                    this.resolved = resolved;
            
            
            
                    let fd = new FormData();
                    fd.append('resolved', resolved);
                    fd.append('_token', '{{ csrf_token() }}');
            
                    this.complete = true;
                    this.submission.complete = true;
            
                    var toPush = this.submission;
            
                    fetch('{{ route('dj.dq.submission.resolve', '') }}/' + this.submission.id, { method: 'POST', body: fd })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.complete = true;
                                this.submission.complete = true;
            
                                if (resolved) {
                                    this.approved.push(toPush);
                                    sessionStorage.setItem('approved', JSON.stringify(this.approved));
                                }
                            }
                        })
            
                },
            
            
            
                init() {
                    this.resolveCode(this.submission.code);
            
                }
            }">



                <div class="flex justify-between items-center">
                    <h2>Submission</h2>



                </div>

                <div>


                    <div class="flex justify-between">
                        <p><strong>Event</strong>: <span x-text="submission.eventName"></span></p>
                        <p><strong>Heat</strong>: <span x-text="submission.heat ?? '-'"></span>
                            <strong>Lane</strong>: <span x-text="submission.lane ?? '-'"></span>
                        </p>
                    </div>

                    <div class="flex justify-between">
                        <p><strong>Team</strong>: <span x-text="submission.teamName"></span></p>
                        <p><strong>Turn</strong>: <span x-text="submission.turn ?? '-'"></span> <strong>Length</strong>:
                            <span x-text="submission.length ?? '-'"></span>
                        </p>
                    </div>


                    <br>


                    <div class="flex space-x-4">
                        <p><strong>Reporter</strong>: <span x-text="submission.name"></span> (<span
                                x-text="submission.position"></span>)
                        </p>
                        <p><strong>Seconder</strong>: <span x-text="submission.seconder.name ?? '-'"></span> (<span
                                x-text="submission.seconder.position ?? '-'"></span>)</p>
                    </div>



                    <br>



                    <h4 class="text-bulsca_red" x-text="submission.code"></h4>



                    <div name="" id="" readonly class="w-full  -mt-2 text-sm text-gray-500 mb-6"
                        x-text="code.description">
                        Manual
                        DQ/P description fills here
                    </div>

                    <p class="font-semibold">Aditional Judge Details</p>
                    <p class="" x-text="submission.details"></p>





                </div>
                <br>
                <div class="grid-2">


                    <button @click="makeDecision(true)" class="flex flex-col items-center justify-center btn btn-thicker "
                        :class="complete && resolved == true ? 'absolute top-0 left-0 w-full h-full' : ''">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>

                        <span x-text="complete ? 'Approved' : 'Approve'"></span>
                    </button>

                    <button @click="makeDecision(false)"
                        class="flex flex-col items-center justify-center  btn btn-thicker btn-danger"
                        :class="complete && resolved == false ? 'absolute top-0 left-0 w-full h-full' : ''">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>


                        <span x-text="complete ? 'Rejected' : 'Reject'"></span>
                    </button>






                </div>









            </div>
        </template>

        <div x-show="showLoader()" x-transition>
            <x-loader />
            <p class="text-sm text-center">Waiting for submissions...</p>
        </div>
        <br>

        <div class="flex items-center justify-between mb-2 mt-6">
            <h3>Approved Submissions</h3>

        </div>


        <template x-for="(submissions, name) in approved" x-key="name">


            <div x-data="{
                open: $persist(true).as(`{{ $comp->id }}-${name}`),
            
            
            }">
                <div class="flex items-center justify-between" @click="open = !open">
                    <h5 x-text="name"></h5>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6 transition-transform" x-bind:class="open ? '' : 'rotate-180'">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                    </svg>

                </div>


                <div x-collapse x-show="open">
                    <template x-for="submission in submissions">
                        <div class="mb-5 relative card" x-ref="form" x-data="{
                        
                        }">





                            <div>


                                <div class="flex justify-between">
                                    <p><strong>Event</strong>: <span x-text="submission.eventName"></span></p>
                                    <p><strong>Heat</strong>: <span x-text="submission.heat ?? '-'"></span>
                                        <strong>Lane</strong>: <span x-text="submission.lane ?? '-'"></span>
                                    </p>
                                </div>

                                <div class="flex justify-between">
                                    <p><strong>Team</strong>: <span x-text="submission.teamName"></span></p>
                                    <p><strong>Turn</strong>: <span x-text="submission.turn ?? '-'"></span>
                                        <strong>Length</strong>:
                                        <span x-text="submission.length ?? '-'"></span>
                                    </p>
                                </div>


                                <br>


                                <div class="flex space-x-4">
                                    <p><strong>Reporter</strong>: <span x-text="submission.name"></span> (<span
                                            x-text="submission.position"></span>)
                                    </p>
                                    <p><strong>Seconder</strong>: <span x-text="submission?.seconder_name ?? '-'"></span>
                                        (<span x-text="submission?.seconder_position ?? '-'"></span>)</p>
                                </div>



                                <br>



                                <h4 class="text-bulsca_red" x-text="submission.code"></h4>



                                <div name="" id="" readonly class="w-full  -mt-2 text-sm text-gray-500 mb-6"
                                    x-text="submission.code_desc">
                                    Manual
                                    DQ/P description fills here
                                </div>

                                <p class="font-semibold">Aditional Judge Details</p>
                                <p class="" x-text="submission.details"></p>


                                <div class=" mt-2" x-data="{
                                
                                    handleRemove() {
                                            if (!confirm(`Are you sure you wan't to remove ${submission.code} for ${submission.teamName} in ${submission.eventName}. This cannot be undone!`)) {
                                                return
                                            }
                                            let fd = new FormData();
                                
                                            fd.append('_token', '{{ csrf_token() }}');
                                
                                            fetch('{{ route('dj.dq.remove', 'X') }}'.replace('X', submission.id), {
                                                method: 'POST',
                                                body: fd
                                            }).then(resp => resp.json()).then(data => {
                                                if (data.success) {
                                                    alert('Submission removed');
                                                } else {
                                                    alert('Failed to remove submission')
                                                }
                                            })
                                
                                        },
                                
                                        handleAppeal() {
                                            if (!confirm(`Are you sure you want to Accept to Appeal ${submission.code} for ${submission.teamName} in ${submission.eventName}. This will remove the DQ/Pen and cannot be undone!`)) {
                                                return
                                            }
                                            let fd = new FormData();
                                
                                            fd.append('_token', '{{ csrf_token() }}');
                                
                                            fetch('{{ route('dj.dq.appeal', 'X') }}'.replace('X', submission.id), {
                                                method: 'POST',
                                                body: fd
                                            }).then(resp => resp.json()).then(data => {
                                                if (data.success) {
                                                    alert('Submission appealed');
                                                    submission.appealed = true
                                                } else {
                                                    alert('Failed to appeal remove submission')
                                                }
                                            })
                                        },
                                
                                }">
                                    {{-- <button class="btn btn-info grow">
                                        Edit
                                    </button> --}}
                                    <div class="grid grid-cols-2 gap-3" x-show="!submission.appealed">
                                        <button class="btn grow" @click="handleAppeal">
                                            Accept Appeal
                                        </button>
                                        <button class="btn btn-danger grow" @click="handleRemove">
                                            Remove
                                        </button>
                                    </div>

                                    <div class="btn grow" x-show="submission.appealed">Appealed</div>
                                </div>





                            </div>


                        </div>
                    </template>
                </div>
            </div>


        </template>





    </div>

@endsection
