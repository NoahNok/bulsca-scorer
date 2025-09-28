@extends('layouts.organisation')



@section('content')
    <h2 class="mb-0">Infractions</h2>
    <p>Create and modify DQ and Penalty codes</p>
    <br>
    <div x-data="{
    
    
        code: {
    
            description: '',
    
            events: {
                @foreach ($eventNames as $eventName)
                    '{{ $eventName }}': { id: null, type: null }, @endforeach
            }
    
        },
    
        newCode: {
            type: 'dq',
            code: '',
            description: '',
            events: {
                @foreach ($eventNames as $eventName)
                    '{{ $eventName }}': { id: null, type: null }, @endforeach
            }
        },
    
    
        urls: {
            get: '{{ route('orgs.infractions.view', [$org, '__type', '__id']) }}',
            create: '{{ route('orgs.infractions.create', $org) }}'
        },
    
        async selectCode(id, type) {
    
            this.code.id = id;
            this.code.type = type;
    
            const res = await fetch(this.urls.get.replace('__type', type).replace('__id', id));
    
    
            if (!res.ok) {
                alert('Failed to load code');
                return;
            }
    
            code = await res.json();
    
            this.code.code = code.code;
            this.code.description = code.description;
    
            Object.keys(this.code.events).forEach(eventName => {
                if (code.events[eventName]) {
                    this.code.events[eventName].id = code.events[eventName].id;
                    this.code.events[eventName].type = code.events[eventName].type;
                } else {
                    this.code.events[eventName].id = null;
                    this.code.events[eventName].type = null;
                }
            });
    
    
            this.modals.codeEdit = true;
        },
    
        async save() {
    
            const res = await fetch(this.urls.get.replace('__type', this.code.type).replace('__id', this.code.id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.code)
            });
    
            if (!res.ok) {
                alert('Failed to save code');
                return;
            }
    
            this.modals.codeEdit = false;
    
        },
    
        openNewCode(type) {
            this.newCode = {
                type: type,
                code: 0,
                description: '',
                events: {
                    @foreach($eventNames as $eventName)
                    '{{ $eventName }}': { id: null, type: null },
                    @endforeach
                }
            };
            this.modals.codeNew = true;
        },
    
        async saveNew() {
    
            if (!this.newCode.code || this.newCode.code <= 0) {
                alert('Code must be a positive integer');
                return;
            }
    
            if (!this.newCode.description || this.newCode.description.trim() === '') {
                alert('Description cannot be empty');
                return;
            }
    
            const res = await fetch(this.urls.create, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(this.newCode)
            });
    
            if (!res.ok) {
                showAlert('Failed to create code');
                return;
            }
    
            let result = await res.json();
            if (result.error) {
                showAlert(result.error);
                return;
            }
    
            window.location.reload();
    
        },
    
        async deleteCode() {
            if (!confirm('Are you sure you want to delete this code? This action cannot be undone.')) {
                return;
            }
    
            const res = await fetch(this.urls.get.replace('__type', this.code.type).replace('__id', this.code.id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
    
            if (!res.ok) {
                alert('Failed to delete code');
                return;
            }
    
            this.modals.codeEdit = false;
            window.location.reload();
        },
    
    
    
    }">
        <div class="grid-2">
            <div>

                <h3>Disqualifications</h3>
                <div class="grid-3 mb-2">



                    @foreach ($dqs as $code)
                        <div class="se-card se-card-body se-card-hover" @click="selectCode({{ $code->id }}, 'dq')">
                            <h4>DQ{{ $code->code }}</h4>
                            <small class=" line-clamp-1 overflow-ellipsis overflow-hidden">{{ $code->description }}</small>
                        </div>
                    @endforeach




                    <x-add-card link="#" @click="openNewCode('dq')" />




                </div>

                {{ $dqs->withQueryString()->links() }}
            </div>


            <div>
                <h3>Penalties</h3>
                <div class="grid-3 mb-2">



                    @foreach ($penalties as $code)
                        <div class="se-card se-card-body se-card-hover" @click="selectCode({{ $code->id }}, 'pen')">
                            <h4>P{{ $code->code }}</h4>
                            <small class=" line-clamp-1 overflow-ellipsis overflow-hidden">{{ $code->description }}</small>
                        </div>
                    @endforeach




                    <x-add-card link="#" @click="openNewCode('pen')" />




                </div>
                {{ $penalties->withQueryString()->links() }}
            </div>
        </div>

        <x-s-e-modal id="codeEdit" title="Edit Code">
            <div x-effect="title = code?.code">
                <div class="se-form-input">
                    <label for="description">Description</label>
                    <textarea name="" id="description" class="border rounded-md p-1" x-model="code.description" cols="30"
                        rows="3"></textarea>
                </div>

                <hr class="spacer">



                <div class="grid-2">
                    @foreach ($eventNames as $eventName)
                        <div class="flex items-center">
                            <label for="event-{{ $eventName }}">{{ $eventName }}</label>
                        </div>
                        <div class="se-form-input imb-0">

                            <select name="" id="" x-model="code.events['{{ $eventName }}'].type">
                                <option value="null">
                                    N/A</option>
                                @foreach (['GENERIC', 'LANE', 'TURN', 'CHANGEOVER', 'CROSSLINE', 'BACKLINE', 'OOF', 'STARTER', 'PICKUP'] as $type)
                                    <option value="{{ $type }}">
                                        {{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            <x-slot name="footer">
                <button type="button" class="se-btn se-btn-danger ml-auto" @click="deleteCode">Delete</button>
                <button type="button" class="se-btn se-btn-success ml-auto" @click="save">Save</button>
            </x-slot>

        </x-s-e-modal>

        <x-s-e-modal id="codeNew" title="New Code">
            <div x-effect="title = (newCode.type == 'dq' ? 'DQ' : 'P') + newCode.code">
                <div class="se-form-input">
                    <label for="description">Code</label>
                    <input type="number" x-model="newCode.code" />
                </div>
                <div class="se-form-input">
                    <label for="description">Description</label>
                    <textarea name="" id="description" class="border rounded-md p-1" x-model="newCode.description" cols="30"
                        rows="3"></textarea>
                </div>

                <hr class="spacer">



                <div class="grid-2">
                    @foreach ($eventNames as $eventName)
                        <div class="flex items-center">
                            <label for="event-{{ $eventName }}">{{ $eventName }}</label>
                        </div>
                        <div class="se-form-input imb-0">

                            <select name="" id="" x-model="newCode.events['{{ $eventName }}'].type">
                                <option value="null">
                                    N/A</option>
                                @foreach (['GENERIC', 'LANE', 'TURN', 'CHANGEOVER', 'CROSSLINE', 'BACKLINE', 'OOF', 'STARTER', 'PICKUP'] as $type)
                                    <option value="{{ $type }}">
                                        {{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                </div>
            </div>

            <x-slot name="footer">
                <button type="button" class="se-btn se-btn-success ml-auto" @click="saveNew">Create</button>
            </x-slot>

        </x-s-e-modal>
    </div>
@endsection
