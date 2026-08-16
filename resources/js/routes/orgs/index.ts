import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import accountsDb024e from './accounts'
import invite from './invite'
import account from './account'
import infractions740e82 from './infractions'
import scoring109674 from './scoring'
import championship from './championship'
/**
* @see \App\Http\Controllers\Organisation\OrganisationController::index
* @see app/Http/Controllers/Organisation/OrganisationController.php:32
* @route '/organisation'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/organisation',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::index
* @see app/Http/Controllers/Organisation/OrganisationController.php:32
* @route '/organisation'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::index
* @see app/Http/Controllers/Organisation/OrganisationController.php:32
* @route '/organisation'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::index
* @see app/Http/Controllers/Organisation/OrganisationController.php:32
* @route '/organisation'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:40
* @route '/organisation/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/organisation/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:40
* @route '/organisation/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:40
* @route '/organisation/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:40
* @route '/organisation/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::store
* @see app/Http/Controllers/Organisation/OrganisationController.php:48
* @route '/organisation'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/organisation',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::store
* @see app/Http/Controllers/Organisation/OrganisationController.php:48
* @route '/organisation'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::store
* @see app/Http/Controllers/Organisation/OrganisationController.php:48
* @route '/organisation'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::show
* @see app/Http/Controllers/Organisation/OrganisationController.php:71
* @route '/organisation/{organisation}'
*/
export const show = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::show
* @see app/Http/Controllers/Organisation/OrganisationController.php:71
* @route '/organisation/{organisation}'
*/
show.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return show.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::show
* @see app/Http/Controllers/Organisation/OrganisationController.php:71
* @route '/organisation/{organisation}'
*/
show.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::show
* @see app/Http/Controllers/Organisation/OrganisationController.php:71
* @route '/organisation/{organisation}'
*/
show.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:81
* @route '/organisation/{organisation}/edit'
*/
export const edit = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:81
* @route '/organisation/{organisation}/edit'
*/
edit.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return edit.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:81
* @route '/organisation/{organisation}/edit'
*/
edit.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:81
* @route '/organisation/{organisation}/edit'
*/
edit.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::update
* @see app/Http/Controllers/Organisation/OrganisationController.php:91
* @route '/organisation/{organisation}'
*/
export const update = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/organisation/{organisation}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::update
* @see app/Http/Controllers/Organisation/OrganisationController.php:91
* @route '/organisation/{organisation}'
*/
update.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return update.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::update
* @see app/Http/Controllers/Organisation/OrganisationController.php:91
* @route '/organisation/{organisation}'
*/
update.put = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::update
* @see app/Http/Controllers/Organisation/OrganisationController.php:91
* @route '/organisation/{organisation}'
*/
update.patch = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::destroy
* @see app/Http/Controllers/Organisation/OrganisationController.php:118
* @route '/organisation/{organisation}'
*/
export const destroy = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/organisation/{organisation}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::destroy
* @see app/Http/Controllers/Organisation/OrganisationController.php:118
* @route '/organisation/{organisation}'
*/
destroy.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return destroy.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::destroy
* @see app/Http/Controllers/Organisation/OrganisationController.php:118
* @route '/organisation/{organisation}'
*/
destroy.delete = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accounts
* @see app/Http/Controllers/Organisation/OrganisationController.php:124
* @route '/organisation/{organisation}/accounts'
*/
export const accounts = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: accounts.url(args, options),
    method: 'get',
})

accounts.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/accounts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accounts
* @see app/Http/Controllers/Organisation/OrganisationController.php:124
* @route '/organisation/{organisation}/accounts'
*/
accounts.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return accounts.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accounts
* @see app/Http/Controllers/Organisation/OrganisationController.php:124
* @route '/organisation/{organisation}/accounts'
*/
accounts.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: accounts.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accounts
* @see app/Http/Controllers/Organisation/OrganisationController.php:124
* @route '/organisation/{organisation}/accounts'
*/
accounts.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: accounts.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::infractions
* @see app/Http/Controllers/Organisation/OrganisationController.php:295
* @route '/organisation/{organisation}/infractions'
*/
export const infractions = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: infractions.url(args, options),
    method: 'get',
})

infractions.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/infractions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::infractions
* @see app/Http/Controllers/Organisation/OrganisationController.php:295
* @route '/organisation/{organisation}/infractions'
*/
infractions.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return infractions.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::infractions
* @see app/Http/Controllers/Organisation/OrganisationController.php:295
* @route '/organisation/{organisation}/infractions'
*/
infractions.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: infractions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::infractions
* @see app/Http/Controllers/Organisation/OrganisationController.php:295
* @route '/organisation/{organisation}/infractions'
*/
infractions.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: infractions.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::scoring
* @see app/Http/Controllers/Organisation/OrganisationController.php:229
* @route '/organisation/{organisation}/scoring'
*/
export const scoring = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scoring.url(args, options),
    method: 'get',
})

scoring.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/scoring',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::scoring
* @see app/Http/Controllers/Organisation/OrganisationController.php:229
* @route '/organisation/{organisation}/scoring'
*/
scoring.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return scoring.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::scoring
* @see app/Http/Controllers/Organisation/OrganisationController.php:229
* @route '/organisation/{organisation}/scoring'
*/
scoring.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scoring.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::scoring
* @see app/Http/Controllers/Organisation/OrganisationController.php:229
* @route '/organisation/{organisation}/scoring'
*/
scoring.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: scoring.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ChampionshipController::championships
* @see app/Http/Controllers/ChampionshipController.php:15
* @route '/organisation/{organisation}/championships'
*/
export const championships = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: championships.url(args, options),
    method: 'get',
})

championships.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/championships',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ChampionshipController::championships
* @see app/Http/Controllers/ChampionshipController.php:15
* @route '/organisation/{organisation}/championships'
*/
championships.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return championships.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChampionshipController::championships
* @see app/Http/Controllers/ChampionshipController.php:15
* @route '/organisation/{organisation}/championships'
*/
championships.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: championships.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChampionshipController::championships
* @see app/Http/Controllers/ChampionshipController.php:15
* @route '/organisation/{organisation}/championships'
*/
championships.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: championships.url(args, options),
    method: 'head',
})

const orgs = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    accounts: Object.assign(accounts, accountsDb024e),
    invite: Object.assign(invite, invite),
    account: Object.assign(account, account),
    infractions: Object.assign(infractions, infractions740e82),
    scoring: Object.assign(scoring, scoring109674),
    championships: Object.assign(championships, championships),
    championship: Object.assign(championship, championship),
}

export default orgs