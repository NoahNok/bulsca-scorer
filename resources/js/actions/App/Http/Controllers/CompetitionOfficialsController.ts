import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CompetitionOfficialsController::index
* @see app/Http/Controllers/CompetitionOfficialsController.php:20
* @route '/comps/{comp}/officials'
*/
export const index = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/officials',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::index
* @see app/Http/Controllers/CompetitionOfficialsController.php:20
* @route '/comps/{comp}/officials'
*/
index.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::index
* @see app/Http/Controllers/CompetitionOfficialsController.php:20
* @route '/comps/{comp}/officials'
*/
index.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::index
* @see app/Http/Controllers/CompetitionOfficialsController.php:20
* @route '/comps/{comp}/officials'
*/
index.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::store
* @see app/Http/Controllers/CompetitionOfficialsController.php:172
* @route '/comps/{comp}/officials'
*/
export const store = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/comps/{comp}/officials',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::store
* @see app/Http/Controllers/CompetitionOfficialsController.php:172
* @route '/comps/{comp}/officials'
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
* @see \App\Http\Controllers\CompetitionOfficialsController::store
* @see app/Http/Controllers/CompetitionOfficialsController.php:172
* @route '/comps/{comp}/officials'
*/
store.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::search
* @see app/Http/Controllers/CompetitionOfficialsController.php:135
* @route '/comps/{comp}/officials/search'
*/
export const search = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(args, options),
    method: 'get',
})

search.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/officials/search',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::search
* @see app/Http/Controllers/CompetitionOfficialsController.php:135
* @route '/comps/{comp}/officials/search'
*/
search.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return search.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::search
* @see app/Http/Controllers/CompetitionOfficialsController.php:135
* @route '/comps/{comp}/officials/search'
*/
search.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::search
* @see app/Http/Controllers/CompetitionOfficialsController.php:135
* @route '/comps/{comp}/officials/search'
*/
search.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: search.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::copy
* @see app/Http/Controllers/CompetitionOfficialsController.php:206
* @route '/comps/{comp}/officials/copy'
*/
export const copy = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: copy.url(args, options),
    method: 'post',
})

copy.definition = {
    methods: ["post"],
    url: '/comps/{comp}/officials/copy',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::copy
* @see app/Http/Controllers/CompetitionOfficialsController.php:206
* @route '/comps/{comp}/officials/copy'
*/
copy.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return copy.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::copy
* @see app/Http/Controllers/CompetitionOfficialsController.php:206
* @route '/comps/{comp}/officials/copy'
*/
copy.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: copy.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::resend
* @see app/Http/Controllers/CompetitionOfficialsController.php:238
* @route '/comps/{comp}/officials/{official}/resend'
*/
export const resend = (args: { comp: number | { id: number }, official: number | { id: number } } | [comp: number | { id: number }, official: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resend.url(args, options),
    method: 'post',
})

resend.definition = {
    methods: ["post"],
    url: '/comps/{comp}/officials/{official}/resend',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::resend
* @see app/Http/Controllers/CompetitionOfficialsController.php:238
* @route '/comps/{comp}/officials/{official}/resend'
*/
resend.url = (args: { comp: number | { id: number }, official: number | { id: number } } | [comp: number | { id: number }, official: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            official: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        official: typeof args.official === 'object'
        ? args.official.id
        : args.official,
    }

    return resend.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{official}', parsedArgs.official.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::resend
* @see app/Http/Controllers/CompetitionOfficialsController.php:238
* @route '/comps/{comp}/officials/{official}/resend'
*/
resend.post = (args: { comp: number | { id: number }, official: number | { id: number } } | [comp: number | { id: number }, official: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resend.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::update
* @see app/Http/Controllers/CompetitionOfficialsController.php:262
* @route '/comps/{comp}/officials/{official}'
*/
export const update = (args: { comp: number | { id: number }, official: number | { id: number } } | [comp: number | { id: number }, official: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/comps/{comp}/officials/{official}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::update
* @see app/Http/Controllers/CompetitionOfficialsController.php:262
* @route '/comps/{comp}/officials/{official}'
*/
update.url = (args: { comp: number | { id: number }, official: number | { id: number } } | [comp: number | { id: number }, official: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            official: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        official: typeof args.official === 'object'
        ? args.official.id
        : args.official,
    }

    return update.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{official}', parsedArgs.official.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::update
* @see app/Http/Controllers/CompetitionOfficialsController.php:262
* @route '/comps/{comp}/officials/{official}'
*/
update.post = (args: { comp: number | { id: number }, official: number | { id: number } } | [comp: number | { id: number }, official: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::destroy
* @see app/Http/Controllers/CompetitionOfficialsController.php:275
* @route '/comps/{comp}/officials/{official}'
*/
export const destroy = (args: { comp: number | { id: number }, official: number | { id: number } } | [comp: number | { id: number }, official: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/comps/{comp}/officials/{official}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::destroy
* @see app/Http/Controllers/CompetitionOfficialsController.php:275
* @route '/comps/{comp}/officials/{official}'
*/
destroy.url = (args: { comp: number | { id: number }, official: number | { id: number } } | [comp: number | { id: number }, official: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            official: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        official: typeof args.official === 'object'
        ? args.official.id
        : args.official,
    }

    return destroy.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{official}', parsedArgs.official.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionOfficialsController::destroy
* @see app/Http/Controllers/CompetitionOfficialsController.php:275
* @route '/comps/{comp}/officials/{official}'
*/
destroy.delete = (args: { comp: number | { id: number }, official: number | { id: number } } | [comp: number | { id: number }, official: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const CompetitionOfficialsController = { index, store, search, copy, resend, update, destroy }

export default CompetitionOfficialsController