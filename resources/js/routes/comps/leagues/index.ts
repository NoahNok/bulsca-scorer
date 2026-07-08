import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\LeagueController::create
* @see app/Http/Controllers/LeagueController.php:12
* @route '/comps/{comp}/leagues/create'
*/
export const create = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/leagues/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LeagueController::create
* @see app/Http/Controllers/LeagueController.php:12
* @route '/comps/{comp}/leagues/create'
*/
create.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { comp: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
    }

    return create.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LeagueController::create
* @see app/Http/Controllers/LeagueController.php:12
* @route '/comps/{comp}/leagues/create'
*/
create.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LeagueController::create
* @see app/Http/Controllers/LeagueController.php:12
* @route '/comps/{comp}/leagues/create'
*/
create.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LeagueController::store
* @see app/Http/Controllers/LeagueController.php:17
* @route '/comps/{comp}/leagues/create'
*/
export const store = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/comps/{comp}/leagues/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LeagueController::store
* @see app/Http/Controllers/LeagueController.php:17
* @route '/comps/{comp}/leagues/create'
*/
store.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { comp: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
    }

    return store.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LeagueController::store
* @see app/Http/Controllers/LeagueController.php:17
* @route '/comps/{comp}/leagues/create'
*/
store.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LeagueController::view
* @see app/Http/Controllers/LeagueController.php:29
* @route '/comps/{comp}/leagues/{league}'
*/
export const view = (args: { comp: number | { id: number }, league: number | { id: number } } | [comp: number | { id: number }, league: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/leagues/{league}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LeagueController::view
* @see app/Http/Controllers/LeagueController.php:29
* @route '/comps/{comp}/leagues/{league}'
*/
view.url = (args: { comp: number | { id: number }, league: number | { id: number } } | [comp: number | { id: number }, league: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            league: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        league: typeof args.league === 'object'
        ? args.league.id
        : args.league,
    }

    return view.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{league}', parsedArgs.league.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LeagueController::view
* @see app/Http/Controllers/LeagueController.php:29
* @route '/comps/{comp}/leagues/{league}'
*/
view.get = (args: { comp: number | { id: number }, league: number | { id: number } } | [comp: number | { id: number }, league: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LeagueController::view
* @see app/Http/Controllers/LeagueController.php:29
* @route '/comps/{comp}/leagues/{league}'
*/
view.head = (args: { comp: number | { id: number }, league: number | { id: number } } | [comp: number | { id: number }, league: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LeagueController::update
* @see app/Http/Controllers/LeagueController.php:34
* @route '/comps/{comp}/leagues/{league}'
*/
export const update = (args: { comp: number | { id: number }, league: number | { id: number } } | [comp: number | { id: number }, league: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/comps/{comp}/leagues/{league}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\LeagueController::update
* @see app/Http/Controllers/LeagueController.php:34
* @route '/comps/{comp}/leagues/{league}'
*/
update.url = (args: { comp: number | { id: number }, league: number | { id: number } } | [comp: number | { id: number }, league: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            league: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        league: typeof args.league === 'object'
        ? args.league.id
        : args.league,
    }

    return update.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{league}', parsedArgs.league.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LeagueController::update
* @see app/Http/Controllers/LeagueController.php:34
* @route '/comps/{comp}/leagues/{league}'
*/
update.post = (args: { comp: number | { id: number }, league: number | { id: number } } | [comp: number | { id: number }, league: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\LeagueController::deleteMethod
* @see app/Http/Controllers/LeagueController.php:44
* @route '/comps/{comp}/leagues/{league}'
*/
export const deleteMethod = (args: { comp: number | { id: number }, league: number | { id: number } } | [comp: number | { id: number }, league: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/comps/{comp}/leagues/{league}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\LeagueController::deleteMethod
* @see app/Http/Controllers/LeagueController.php:44
* @route '/comps/{comp}/leagues/{league}'
*/
deleteMethod.url = (args: { comp: number | { id: number }, league: number | { id: number } } | [comp: number | { id: number }, league: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            league: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        league: typeof args.league === 'object'
        ? args.league.id
        : args.league,
    }

    return deleteMethod.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{league}', parsedArgs.league.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LeagueController::deleteMethod
* @see app/Http/Controllers/LeagueController.php:44
* @route '/comps/{comp}/leagues/{league}'
*/
deleteMethod.delete = (args: { comp: number | { id: number }, league: number | { id: number } } | [comp: number | { id: number }, league: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

const leagues = {
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    view: Object.assign(view, view),
    update: Object.assign(update, update),
    delete: Object.assign(deleteMethod, deleteMethod),
}

export default leagues