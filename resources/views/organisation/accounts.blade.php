@extends('layouts.organisation')



@section('content')
    <h2 class="mb-0">Accounts</h2>
    <br>
    <div>


        <div class="se-table se-table-thin">
            <table>


                <tbody>
                    @foreach ($org->getAccounts() as $account)
                        <tr>
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
        }">


            @csrf



            <div class="se-form-input">
                <input type="text" name="email" id="email" required placeholder="Email Address">
            </div>

            <div class="grid-4 gap-1!">
                @foreach (App\Models\Competition::$accessTypes as $type => $name)
                    <div class="flex space-x-2">
                        <input type="checkbox" name="access[]" value="{{ $type }}" id="access-{{ $type }}">
                        <label for="access-{{ $type }}" class="font-archivo flex items-center">
                            {{ $name }}
                        </label>
                    </div>
                @endforeach
            </div>







        </form>

        <x-slot name="footer">
            <button type="submit" form="orgAddAccountForm" class="se-btn se-btn-success ml-auto">Add</button>
        </x-slot>
    </x-s-e-modal>
@endsection
