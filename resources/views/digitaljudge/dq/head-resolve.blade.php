@extends('digitaljudge.mpa-layout')

@section('title', 'DQ/Penalty')
@php
    $backlink = false;
    $icon = '<path stroke-linecap="round" stroke-linejoin="round" d="M15.182 16.318A4.486 4.486 0 0012.016 15a4.486 4.486 0 00-3.198 1.318M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />';
@endphp

@section('content')


    <div class="flex flex-col  " x-data="{
        found: [],
    
    
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
    
                this.found = []
    
                this.found = data.result;
            })
        },
    
        init() {
    
    
            this.fetchData();
    
            setInterval(() => {
                this.fetchData();
            }, 3000);
    
    
    
        }
    }">

        <template x-for="sub in found">

            <div class="mb-20 relative" x-ref="form" x-data="{
            
                submission: sub,
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
            
            
                    fetch('{{ route('dj.dq.submission.resolve', '') }}/' + this.sub.id, { method: 'POST', body: fd })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                this.complete = true;
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

                <div x-show="showContent" x-collapse>


                    <div class="flex justify-between">
                        <p><strong>Event</strong>: <span x-text="submission.eventName"></span></p>
                        <p><strong>Heat</strong>: <span x-text="submission.heat"></span>
                            <strong>Lane</strong>: <span x-text="submission.lane"></span>
                        </p>
                    </div>

                    <div class="flex justify-between">
                        <p><strong>Team</strong>: <span x-text="submission.teamName"></span></p>
                        <p><strong>Turn</strong>: <span x-text="submission.turn"></span> <strong>Length</strong>: <span
                                x-text="submission.length"></span></p>
                    </div>


                    <br>


                    <div class="flex space-x-4">
                        <p><strong>Reporter</strong>: <span x-text="submission.name"></span> (<span
                                x-text="submission.position"></span>)
                        </p>
                        <p><strong>Seconder</strong>: <span x-text="submission.seconder.name"></span> (<span
                                x-text="submission.seconder.position"></span>)</p>
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

        <div x-show="found.length == 0" x-transition>
            <x-loader />
            <p class="text-sm text-center">Waiting for submissions...</p>
        </div>





    </div>

@endsection
