@extends('layouts.app')

@section('title', 'Create Organisation')

@section('content')

    <div x-data="{
    
        loading: false,
    
        form: {
            name: null,
            subdomain: null,
            logo: null,
        },
    
        taken: {
            name: false,
            subdomain: false,
        },
    
        logoPreview: null,
    
        checkNameAndSubdomain() {
    
    
    
            if (!this.form.name || this.form.name.trim() == '') {
                return
            }
    
            let fd = new FormData()
            fd.append('name', this.form.name)
            fd.append('subdomain', this.form.subdomain)
    
            fetch('{{ route('orgs.name-sub-taken') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: fd
            }).then(resp => resp.json()).then(data => {
                this.taken.name = data.name
                this.taken.subdomain = data.subdomain
            })
        },
    
        subdomainFormatted() {
            if (this.form.name == null) {
                return
            }
            this.form.subdomain = this.form.name.toLowerCase().trim().replaceAll(/\s+/g, ' ').replaceAll(' ', '-')
        },
    
        async getLogoPreview(file) {
    
            console.log('h')
            if (!file) {
                return null
            }
    
            if (!file.type.startsWith('image/')) return;
    
    
            this.logoPreview = this.readFileAsDataURL(file)
    
        },
        readFileAsDataURL(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = e => resolve(e.target.result);
                reader.onerror = err => reject(err);
                reader.readAsDataURL(file);
            });
        }
    
    
    
    
    }">
        <h1>Organisation Creator</h1>
        <br>





        <div class="flex flex-row items-center space-x-3">


            <div>


                <div @click="$refs.logo.click()"
                    class="size-32 rounded-full flex items-center justify-center relative   cursor-pointer hover:text-se overflow-hidden">
                    <img :src="logoPreview" alt="">
                    <div class="absolute w-full h-full flex items-center justify-center p-2 "
                        :class="logoPreview ? 'bg-gray-200/30 hover:bg-gray-700/80' : 'bg-gray-200'">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-7">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>

                    </div>

                </div>
                <input type="file" name="where" id="where" accept="image/*" placeholder="Where"
                    style="display: none" x-ref="logo" @change="getLogoPreview($event.target.files[0])" required>
            </div>

            <div class="se-form-input imb-0 col-span-2 w-1/4">

                <input type="text" name="name" id="name" placeholder="Name" x-model="form.name" required
                    @keyup="subdomainFormatted" @keyup.debounce="checkNameAndSubdomain">
            </div>

            <div class="se-form-input" x-show="false">
                <label for="when">Subdomain</label>
                <input type="text" placeholder="my-org" x-model="form.subdomain" required>
            </div>








            <div class="se-card flex-row! items-center justify-between xl:w-1/3 ml-auto">
                <div>
                    <h3 x-text="form.name || 'My Org'">Org Name</h3>
                    <small><span x-text="form.subdomain || 'my-org'"></span>.scoring.local</small>
                </div>

                <div class="size-10 rounded-full flex items-center justify-center relative">
                    <img src="" alt="">
                    <div class="absolute w-full h-full flex items-center justify-center">
                        <h2 class="mb-0! text-xl font-archivo font-semibold">S.<span class="text-se">E</span></h2>
                    </div>

                </div>
            </div>

        </div>







        <div class="flex" @click="createCompetition">
            <x-loading-button class="se-btn-success ml-auto">Create</x-loading-button>


        </div>


    </div>



@endsection
