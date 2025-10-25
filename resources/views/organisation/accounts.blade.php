@extends('layouts.organisation')



@section('content')
    <h2 class="mb-0">Accounts</h2>
    <p>Click an invite to cancel it.</p>
    <br>
    <div>


        <div class="se-table se-table-thin">
            <table x-data="{
                async cancelInvite(id) {
                    if (!await askConfirm('Are you sure you want to cancel this invite?')) { return }
                    window.location.href = '{{ route('orgs.invite.cancel', [$org, '__id']) }}'.replace('__id', id)
                }
            
            }">


                <tbody>
                    @foreach ($org->getAccounts() as $account)
                        <tr
                            @click="() => {
                            modals.orgEditAccount = true
                            modals.data.orgEditAccount.id = {{ $account['id'] }}
                            }">
                            <th>{{ $account['name'] }}</th>
                            <td>
                                @if (count($account['access']) > 2)
                                    {{ implode(', ', array_slice($account['access'], 0, 2)) }} &
                                    {{ count($account['access']) - 2 }} more
                                @else
                                    {{ implode(', ', $account['access']) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @foreach ($org->getInvites as $invite)
                        <tr @click="cancelInvite('{{ $invite->id }}')">
                            <th>{{ $invite->email }}</th>
                            <td>
                                Invited
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <br>
        <x-add-card text="Competition" @click="modals.orgAddAccount = true"></x-add-card>


    </div>

    <x-s-e-modal id="orgAddAccount" title="Add Account">

        <form id="orgAddAccountForm"
            x-on:submit="(e) => {
            e.preventDefault()
           
      
            
            if (this.loading) {
                return
            }

            this.loading = true


            fetch('{{ route('orgs.accounts.post', $org) }}', {
                method: 'POST',
                body: new FormData($event.target),
                headers: {
                    'Accept': 'application/json',
                    
                }
            }).then(resp => {
                this.loading = false
                if (!resp.ok && resp.status != 422) {
                    showAlert('Something went wrong, account was not created.')
             
                    return
                }

              
                return resp.json()

               
            }).then(data => {

                
                if (data.errors) {
                    showAlert(Object.entries(data.errors)[0][1])
                    return
                }

                if (data.error) {
                    showAlert(data.error)
                    return
                }

                window.location.reload()
            })
        }"
            x-data="{
                accounts: [],
                email: '',
            
                searchEmail() {
                    email = this.email.trim()
                    if (email.length < 3) {
                        return
                    }
            
                    fetch('{{ route('accounts.search', '__id') }}'.replace('__id', email)).then(resp => resp.json()).then(data => {
                        this.accounts = data
                    })
                }
            }">


            @csrf


            <p class="text-sm">Invite someone to this organisaiton. If they have an account it will appear as you type their
                email,
                otherwise they will be invited to create an account.</p>
            <br>
            <div class="se-form-input relative">
                <input type="text" name="email" id="email" required placeholder="Email Address" x-model="email"
                    @keyup.debounce.200ms="searchEmail">



                <div class="absolute w-full top-2/3 left-0  bg-white border" x-show="accounts.length > 0" x-cloak>
                    <template x-for="account in accounts">
                        <div class="se-card se-card-hover p-2! text-sm! flex-row! rounded-none! items-center justify-between"
                            @click="() => {email = account.email; accounts = []}">
                            <span x-text="account.name"></span>
                            <small x-text="account.email"></small>
                        </div>
                    </template>
                </div>
            </div>

            <strong>Access</strong>
            <p class="text-sm">Please select the access the user should have. It is recommended that they atleast have
                'view' access. <br><br>Applying <strong>admin</strong> will also grant all competition access</p>

            <br>

            <div class="grid-3 gap-1!">
                @foreach (App\Models\Organisation\Organisation::$accessTypes as $group => $access)
                    <h4 class=" col-span-3 mt-2 first-of-type:mt-0">{{ $group }}</h4>
                    @foreach ($access as $type => $name)
                        <div class="flex space-x-2">
                            <input type="checkbox" name="access[]" value="{{ $type }}"
                                @if ($type == 'view') checked @endif id="access-{{ $type }}">
                            <label for="access-{{ $type }}" class="font-archivo flex items-center">
                                {{ $name }}
                            </label>
                        </div>
                    @endforeach
                @endforeach
            </div>







        </form>

        <x-slot name="footer">
            <button type="submit" form="orgAddAccountForm" class="se-btn se-btn-success ml-auto">Add</button>
        </x-slot>
    </x-s-e-modal>

    <x-s-e-modal id="orgEditAccount" title="Edit Account">

        <form id="orgEditAccountForm"
            x-on:submit="(e) => {
            e.preventDefault()
           
            
            if (this.loading) {
                return
            }

            this.loading = true


            fetch('{{ route('orgs.accounts.edit', [$org, 'account' => '__id']) }}'.replace('__id', modals.data.orgEditAccount?.id), {
                method: 'POST',
                body: new FormData($event.target),
                headers: {
                    'Accept': 'application/json',
                    
                }
            }).then(resp => {
                this.loading = false
                if (!resp.ok && resp.status != 422) {
                    showAlert('Something went wrong, unable to save changes')
                  
                    return null
                }

              
                return resp.json()

               
            }).then(data => {

                

                if (data.errors) {
                    showAlert(Object.entries(data.errors)[0][1])
                    return
                }

                closeModal()
                
                window.location.reload()
            })
        }"
            x-data="{
                data: {
                    name: '',
            
            
                    access: [],
                    owner: false,
                },
            
                fetchAccount(id) {
                    this.loading = true
            
                    fetch('{{ route('orgs.accounts.view', [$org, 'account' => '__id']) }}'.replace('__id', id))
                        .then(resp => resp.json())
                        .then(rdata => {
            
                            if (rdata.error) {
                                showAlert(rdata.error);
                                return;
                            }
            
                            this.data.name = rdata.name;
                            this.data.email = rdata.email;
            
                            rdata.access.forEach(access => {
                                let target = $el.querySelector(`#edit-access-${access}`)
                                if (target) {
                                    target.checked = true
                                }
            
            
                            })
            
                            this.data.owner = rdata.access.includes('owner')
            
            
                        })
                        .catch(err => {
                            console.error('Failed to fetch account:', err);
                            showAlert('Failed to load account details');
                        }).finally(() => {
                            this.loading = false;
                        });
                },
            
            
            }" x-init="() => {
            
                $watch('modals.data.orgEditAccount?.id', value => {
                    if (value == undefined) {
                        return;
                    }
            
            
            
                    fetchAccount(value)
            
                })
            
            }">


            @csrf

            <h3 x-text="data.name"></h3>
            <p class="text-sm text-gray-400 mb-4" x-text="data.email"></p>


            <p class="text-sm" x-show="!data.owner">Applying <strong>admin</strong> will also grant all competition access
            </p>

            <br>

            <div class="grid-3 gap-1!" x-show="!data.owner">
                @foreach (App\Models\Organisation\Organisation::$accessTypes as $group => $access)
                    <h4 class=" col-span-3 mt-2 first-of-type:mt-0">{{ $group }}</h4>
                    @foreach ($access as $type => $name)
                        <div class="flex space-x-2">
                            <input type="checkbox" name="access[]" value="{{ $type }}"
                                id="edit-access-{{ $type }}">
                            <label for="edit-access-{{ $type }}" class="font-archivo flex items-center">
                                {{ $name }}
                            </label>
                        </div>
                    @endforeach
                @endforeach
            </div>

            <p x-show="data.owner">
                You own this organisation
            </p>


        </form>
        <br>
        <form x-on:submit.prevent="removeAccount()" method="POST" action="{{ route('orgs.account.remove', $org) }}"
            x-data="{
                async removeAccount() {
                    if (!await askConfirm('Are you sure you want to remove this account?')) { return false }
            
                    $refs.rmid.value = modals.data.orgEditAccount?.id
            
                    $el.submit()
                }
            }">
            @method('DELETE')
            @csrf

            <input x-ref="rmid" type="hidden" name="id">
            <button class="se-btn se-btn-danger">Remove Account</button>
        </form>

        <x-slot name="footer">

            <button type="submit" form="orgEditAccountForm" class="se-btn se-btn-success ml-auto">Save</button>


        </x-slot>
    </x-s-e-modal>
@endsection
