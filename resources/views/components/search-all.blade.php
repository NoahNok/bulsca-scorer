<div class="relative" x-data="{
    results: [],
    search: '',
    hide: false,

    doSearch() {
        let search = this.search.trim()

        if (search == '') {
            return
        }

        fetch('{{ route('search', '__search') }}'.replace('__search', search)).then(resp => resp.json()).then(data => {
            this.results = data
        })

    }
}" @click.outside="hide = true" @click="hide = false">
    <div class="se-form-input mb-0!">
        <input type="text" class=" border-2 rounded-md text-lg h-12!  "
            placeholder="Search competitions and organisations" @keyup.throttle="doSearch" x-model="search">
    </div>

    <div x-show="search.trim() != '' && !hide" x-cloak
        class="absolute top-full z-40 scale-102 left-0 w-full bg-white rounded-md border-2 border-gray-300 p-4 hover:border-gray-400">
        <div x-show="results?.comps?.length > 0">
            <h3>Competitions</h3>
            <div class="grid md:grid-cols-2 gap-2">
                <template x-for="comp in results?.comps">
                    <a :href="comp.url"
                        class="p-2 px-3 hover:bg-gray-100 cursor-pointer transition-colors rounded-md">
                        <h4 x-text="comp.name"></h4>
                        <p class="font-semibold uppercase text-xs! flex items-center space-x-3 text-gray-600">
                            <span x-text="comp.when"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d=" m6.115 5.19.319 1.913A6 6 0 0 0 8.11 10.36L9.75
                                                                                                                                                                                                                                                                                                                                                12l-.387.775c-.217.433-.132.956.21 1.298l1.348 1.348c.21.21.329.497.329.795v1.089c0 .426.24.815.622
                                                                                                                                                                                                                                                                                                                                                1.006l.153.076c.433.217.956.132 1.298-.21l.723-.723a8.7 8.7 0 0 0 2.288-4.042 1.087 1.087 0 0
                                                                                                                                                                                                                                                                                                                                                0-.358-1.099l-1.33-1.108c-.251-.21-.582-.299-.905-.245l-1.17.195a1.125 1.125 0 0 1-.98-.314l-.295-.295a1.125
                                                                                                                                                                                                                                                                                                                                                1.125 0 0 1 0-1.591l.13-.132a1.125 1.125 0 0 1 1.3-.21l.603.302a.809.809 0 0 0 1.086-1.086L14.25
                                                                                                                                                                                                                                                                                                                                                7.5l1.256-.837a4.5 4.5 0 0 0 1.528-1.732l.146-.292M6.115 5.19A9 9 0 1 0 17.18 4.64M6.115 5.19A8.965 8.965 0 0 1
                                                                                                                                                                                                                                                                                                                                                12 3c1.929 0 3.716.607 5.18 1.64" />
                            </svg>
                            <span x-text="comp.where"></span>
                        </p>
                    </a>
                </template>
            </div>
        </div>
        <div x-show="results?.comps?.length > 0 && results?.orgs?.length > 0">
            <br>
            <hr class="spacer">
            <br>
        </div>

        <div x-show="results?.orgs?.length > 0">
            <h3>Organisations</h3>
            <div class="grid md:grid-cols-4 gap-2">
                <template x-for="org in results?.orgs">
                    <a :href="org.url"
                        class="p-2 px-3 hover:bg-gray-100 cursor-pointer transition-colors rounded-md flex items-center justify-between">
                        <h4 x-text="org.name"></h4>
                        <img :src="org.logo" alt="" class="size-6 rounded-full">
                    </a>
                </template>
            </div>
        </div>

        <div x-show="(results?.comps?.length > 0 || results?.orgs?.length > 0) && results?.championships?.length > 0">
            <br>
            <hr class="spacer">
            <br>
        </div>

        <div x-show="results?.championships?.length > 0">
            <h3>Championships</h3>
            <div class="grid md:grid-cols-4 gap-2">
                <template x-for="championship in results?.championships">
                    <a :href="championship.url"
                        class="p-2 px-3 hover:bg-gray-100 cursor-pointer transition-colors rounded-md flex items-center justify-between">
                        <h4 x-text="championship.name"></h4>
                    </a>
                </template>
            </div>
        </div>


        

        <div x-show="results?.comps?.length + results?.orgs?.length + results?.championships?.length == 0">
            <p>You've reached the end of Scoring.Events. Venturing further could be risky!</p>
        </div>
    </div>
</div>
