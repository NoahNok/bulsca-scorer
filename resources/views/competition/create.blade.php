@extends('layouts.app')

@section('title', 'Create Competition')

@section('content')

    <div x-data="{
    
        step: 1,
        type: null,
    
        form: {
            org: null,
        },
    
        search: {
            org: '',
        },
    
        loading: false,
    
        orgSearch(org) {
            if (this.search.org == '') {
                return true
            }
            return org.toLowerCase().startsWith(this.search.org.toLowerCase())
        },
    
        selectOrg(ordId) {
            this.form.org = ordId;
            this.step = 2;
        },
    
        selectOrga() {
    
    
            if (this.step >= 2 && this.type != 'organisation' && this.form.org == null) {
                this.step = 1
    
            }
    
            this.type = 'organisation';
    
    
        },
    
        selectAcc() {
    
    
    
            this.step = 2
            this.form.org = null
    
    
            this.type = 'account';
    
    
        },
    
        createCompetition() {
    
    
    
            if (!this.$refs.createForm.reportValidity()) {
                return
            }
    
            if (this.loading) {
                return
            }
    
            this.loading = true
    
            let formData = new FormData(this.$refs.createForm)
    
            formData.append('org', this.form.org)
    
            fetch('{{ route('comps.create.post') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
    
            }).then(resp => {
    
    
    
                if (!resp.ok) {
                    showAlert('Something went wrong, unable to create competition')
                    this.loading = false
                    return null;
                }
    
                return resp.json()
            }).then(data => {
                if (!data) {
                    return
                }
    
                if (data.url) {
                    window.location.href = data.url
                }
            })
        },
    
        onInit() {
            const params = new URLSearchParams(window.location.search);
            const type = params.get('type');
    
    
            if (type === null) {
                return
            } else if (type === 'acc') {
                this.selectAcc()
            } else if (type === 'org') {
                this.selectOrga()
            }
        }
    
    }" x-init="onInit">
        <h1>Competition Creator</h1>
        <br>

        <div class="grid-2">
            <div class="se-card se-card-hover" :class="type === 'account' ? 'se-card-active' : ''" @click="selectAcc()">
                <div class="flex items-center justify-between h-full">
                    <div>
                        <h4>My Account</h3>
                            <p>This competition will be registered under your own account, and only those you invite can
                                manage
                                it.</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-8 self-center justify-self-center">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>

                </div>
            </div>
            <div class="se-card se-card-hover" :class="type === 'organisation' ? 'se-card-active' : ''"
                @click="selectOrga()">

                <div class="flex items-center justify-between space-x-6">
                    <div>
                        <h4>An Organisation</h4>
                        <p>This competition will be registered under your organisation, and only those with organisational
                            access and
                            those you invite can manage it.</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                    </svg>

                </div>

            </div>
        </div>

        <br>

        <div x-show="type === 'organisation'" x-cloak>

            <div class="flex items-center justify-between">
                <h3>Select an Organisation</h3>
                <div class="se-form-input  mb-0!">
                    <input type="text" placeholder="Search..." class="mb-0! text-right! text-sm! h-8!"
                        x-model="search.org">
                </div>
            </div>
            <div class="grid-4 mt-2">
                <div class="se-card se-card-hover" @click="selectOrg(1)" :class="form.org == 1 ? 'se-card-active' : ''"
                    x-show="orgSearch('BULSCA')">
                    <h4>BULSCA</h4>
                </div>
                <div class="se-card se-card-hover" @click="selectOrg(2)" :class="form.org == 2 ? 'se-card-active' : ''"
                    x-show="orgSearch('RLSS')">
                    <h4>RLSS</h4>
                </div>
            </div>
            <br>
        </div>




        <div x-show="step >= 2" x-cloak>
            <h3>Details</h3>
            <div x-cloak x-show="type != null">


                <form x-ref="createForm">
                    <div class="grid-4">


                        <div class="se-form-input col-span-2">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" placeholder="Name"
                                value="{{ old('name') }}" required>
                        </div>

                        <div class="se-form-input">
                            <label for="when">Date</label>
                            <input type="datetime-local" name="when" id="when" placeholder="When"
                                value="{{ old('when') }}" required>
                        </div>

                        <div class="se-form-input">
                            <label for="where">Location</label>
                            <input type="text" name="where" id="where" placeholder="Where"
                                value="{{ old('where') }}" required>
                        </div>



                        <div class="se-form-input">
                            <label for="lanes">Lanes</label>
                            <input type="number" name="lanes" id="lanes" placeholder="Lanes"
                                value="{{ old('lanes') }}" required>
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
                </form>


            </div>

            <hr class="spacer">
            <br>
            <div class="flex" @click="createCompetition">
                <x-loading-button class="se-btn-success ml-auto">Create</x-loading-button>


            </div>
        </div>

    </div>



@endsection
