import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\ChampionshipController::create
* @see app/Http/Controllers/ChampionshipController.php:30
* @route '/organisation/{organisation}/championships/create'
*/
export const create = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/championships/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ChampionshipController::create
* @see app/Http/Controllers/ChampionshipController.php:30
* @route '/organisation/{organisation}/championships/create'
*/
create.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return create.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChampionshipController::create
* @see app/Http/Controllers/ChampionshipController.php:30
* @route '/organisation/{organisation}/championships/create'
*/
create.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChampionshipController::create
* @see app/Http/Controllers/ChampionshipController.php:30
* @route '/organisation/{organisation}/championships/create'
*/
create.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ChampionshipController::store
* @see app/Http/Controllers/ChampionshipController.php:35
* @route '/organisation/{organisation}/championships/create'
*/
export const store = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/championships/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ChampionshipController::store
* @see app/Http/Controllers/ChampionshipController.php:35
* @route '/organisation/{organisation}/championships/create'
*/
store.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChampionshipController::store
* @see app/Http/Controllers/ChampionshipController.php:35
* @route '/organisation/{organisation}/championships/create'
*/
store.post = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ChampionshipController::view
* @see app/Http/Controllers/ChampionshipController.php:23
* @route '/organisation/{organisation}/championships/{championship}'
*/
export const view = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/championships/{championship}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ChampionshipController::view
* @see app/Http/Controllers/ChampionshipController.php:23
* @route '/organisation/{organisation}/championships/{championship}'
*/
view.url = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            championship: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        championship: typeof args.championship === 'object'
        ? args.championship.id
        : args.championship,
    }

    return view.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{championship}', parsedArgs.championship.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChampionshipController::view
* @see app/Http/Controllers/ChampionshipController.php:23
* @route '/organisation/{organisation}/championships/{championship}'
*/
view.get = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChampionshipController::view
* @see app/Http/Controllers/ChampionshipController.php:23
* @route '/organisation/{organisation}/championships/{championship}'
*/
view.head = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ChampionshipController::addCompetition
* @see app/Http/Controllers/ChampionshipController.php:48
* @route '/organisation/{organisation}/championships/{championship}/add-competition'
*/
export const addCompetition = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: addCompetition.url(args, options),
    method: 'get',
})

addCompetition.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/championships/{championship}/add-competition',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ChampionshipController::addCompetition
* @see app/Http/Controllers/ChampionshipController.php:48
* @route '/organisation/{organisation}/championships/{championship}/add-competition'
*/
addCompetition.url = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            championship: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        championship: typeof args.championship === 'object'
        ? args.championship.id
        : args.championship,
    }

    return addCompetition.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{championship}', parsedArgs.championship.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChampionshipController::addCompetition
* @see app/Http/Controllers/ChampionshipController.php:48
* @route '/organisation/{organisation}/championships/{championship}/add-competition'
*/
addCompetition.get = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: addCompetition.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ChampionshipController::addCompetition
* @see app/Http/Controllers/ChampionshipController.php:48
* @route '/organisation/{organisation}/championships/{championship}/add-competition'
*/
addCompetition.head = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: addCompetition.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ChampionshipController::associateCompetition
* @see app/Http/Controllers/ChampionshipController.php:60
* @route '/organisation/{organisation}/championships/{championship}/associate-competition'
*/
export const associateCompetition = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: associateCompetition.url(args, options),
    method: 'post',
})

associateCompetition.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/championships/{championship}/associate-competition',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ChampionshipController::associateCompetition
* @see app/Http/Controllers/ChampionshipController.php:60
* @route '/organisation/{organisation}/championships/{championship}/associate-competition'
*/
associateCompetition.url = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            championship: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        championship: typeof args.championship === 'object'
        ? args.championship.id
        : args.championship,
    }

    return associateCompetition.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{championship}', parsedArgs.championship.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChampionshipController::associateCompetition
* @see app/Http/Controllers/ChampionshipController.php:60
* @route '/organisation/{organisation}/championships/{championship}/associate-competition'
*/
associateCompetition.post = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: associateCompetition.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ChampionshipController::deassociateCompetition
* @see app/Http/Controllers/ChampionshipController.php:88
* @route '/organisation/{organisation}/championships/{championship}/deassociate-competition'
*/
export const deassociateCompetition = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deassociateCompetition.url(args, options),
    method: 'post',
})

deassociateCompetition.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/championships/{championship}/deassociate-competition',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ChampionshipController::deassociateCompetition
* @see app/Http/Controllers/ChampionshipController.php:88
* @route '/organisation/{organisation}/championships/{championship}/deassociate-competition'
*/
deassociateCompetition.url = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            championship: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        championship: typeof args.championship === 'object'
        ? args.championship.id
        : args.championship,
    }

    return deassociateCompetition.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{championship}', parsedArgs.championship.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ChampionshipController::deassociateCompetition
* @see app/Http/Controllers/ChampionshipController.php:88
* @route '/organisation/{organisation}/championships/{championship}/deassociate-competition'
*/
deassociateCompetition.post = (args: { organisation: number | { id: number }, championship: number | { id: number } } | [organisation: number | { id: number }, championship: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deassociateCompetition.url(args, options),
    method: 'post',
})

const championship = {
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    view: Object.assign(view, view),
    addCompetition: Object.assign(addCompetition, addCompetition),
    associateCompetition: Object.assign(associateCompetition, associateCompetition),
    deassociateCompetition: Object.assign(deassociateCompetition, deassociateCompetition),
}

export default championship