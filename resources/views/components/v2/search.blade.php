{{--
    Search competitions, Organisations and Championships by name, on the /search/{search} JSON endpoint.
    Results open in a panel under the input. Used on the home page's hero and on Explore.
--}}
<div class="v2-search" x-data="{
    results: {},
    search: '',
    hide: false,

    doSearch() {
        let search = this.search.trim()

        if (search == '') {
            return
        }

        fetch('{{ route('search', '__search') }}'.replace('__search', encodeURIComponent(search))).then(resp => resp.json()).then(data => {
            this.results = data
        })
    },

    get count() {
        return (this.results?.comps?.length ?? 0) + (this.results?.orgs?.length ?? 0) + (this.results?.championships?.length ?? 0)
    }
}" @click.outside="hide = true" @click="hide = false" @keydown.escape="hide = true">
    <label class="sr-only" for="v2-search-input">Search competitions and organisations</label>
    <input id="v2-search-input" type="search" class="v2-input v2-search-input" autocomplete="off"
        placeholder="Search competitions and organisations" @keyup.throttle="doSearch" x-model="search">

    <div class="v2-search-results" x-show="search.trim() != '' && !hide" x-cloak>
        <section x-show="results?.comps?.length > 0">
            <h3 class="v2-search-heading">Competitions</h3>
            <div class="v2-search-grid is-wide">
                <template x-for="comp in results?.comps">
                    <a :href="comp.url" class="v2-search-item">
                        <span class="v2-search-name" x-text="comp.name"></span>
                        <span class="v2-search-detail">
                            <span x-text="comp.when"></span>
                            <span aria-hidden="true">·</span>
                            <span x-text="comp.where"></span>
                        </span>
                    </a>
                </template>
            </div>
        </section>

        <section x-show="results?.orgs?.length > 0">
            <h3 class="v2-search-heading">Organisations</h3>
            <div class="v2-search-grid">
                <template x-for="org in results?.orgs">
                    <a :href="org.url" class="v2-search-item is-row">
                        <img :src="org.logo" alt="" class="v2-search-logo">
                        <span class="v2-search-name" x-text="org.name"></span>
                    </a>
                </template>
            </div>
        </section>

        <section x-show="results?.championships?.length > 0">
            <h3 class="v2-search-heading">Championships</h3>
            <div class="v2-search-grid">
                <template x-for="championship in results?.championships">
                    <a :href="championship.url" class="v2-search-item">
                        <span class="v2-search-name" x-text="championship.name"></span>
                    </a>
                </template>
            </div>
        </section>

        <p class="v2-search-empty" x-show="count == 0">You've reached the end of Scoring.Events. Venturing further could be risky!</p>
    </div>
</div>
