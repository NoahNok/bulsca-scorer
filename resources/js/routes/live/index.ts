import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import dqs7b7abb from './dqs'
/**
* @see \App\Http\Controllers\LiveController::all
* @see app/Http/Controllers/LiveController.php:47
* @route '//live.localhost/all'
*/
export const all = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: all.url(options),
    method: 'get',
})

all.definition = {
    methods: ["get","head"],
    url: '//live.localhost/all',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LiveController::all
* @see app/Http/Controllers/LiveController.php:47
* @route '//live.localhost/all'
*/
all.url = (options?: RouteQueryOptions) => {
    return all.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LiveController::all
* @see app/Http/Controllers/LiveController.php:47
* @route '//live.localhost/all'
*/
all.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: all.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LiveController::all
* @see app/Http/Controllers/LiveController.php:47
* @route '//live.localhost/all'
*/
all.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: all.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LiveController::dqs
* @see app/Http/Controllers/LiveController.php:95
* @route '//live.localhost/dqs'
*/
export const dqs = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dqs.url(options),
    method: 'get',
})

dqs.definition = {
    methods: ["get","head"],
    url: '//live.localhost/dqs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LiveController::dqs
* @see app/Http/Controllers/LiveController.php:95
* @route '//live.localhost/dqs'
*/
dqs.url = (options?: RouteQueryOptions) => {
    return dqs.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LiveController::dqs
* @see app/Http/Controllers/LiveController.php:95
* @route '//live.localhost/dqs'
*/
dqs.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dqs.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LiveController::dqs
* @see app/Http/Controllers/LiveController.php:95
* @route '//live.localhost/dqs'
*/
dqs.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dqs.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LiveController::data
* @see app/Http/Controllers/LiveController.php:52
* @route '//live.localhost/{comp}'
*/
export const data = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(args, options),
    method: 'get',
})

data.definition = {
    methods: ["get","head"],
    url: '//live.localhost/{comp}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LiveController::data
* @see app/Http/Controllers/LiveController.php:52
* @route '//live.localhost/{comp}'
*/
data.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return data.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LiveController::data
* @see app/Http/Controllers/LiveController.php:52
* @route '//live.localhost/{comp}'
*/
data.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: data.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LiveController::data
* @see app/Http/Controllers/LiveController.php:52
* @route '//live.localhost/{comp}'
*/
data.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: data.url(args, options),
    method: 'head',
})

const live = {
    all: Object.assign(all, all),
    dqs: Object.assign(dqs, dqs7b7abb),
    data: Object.assign(data, data),
}

export default live