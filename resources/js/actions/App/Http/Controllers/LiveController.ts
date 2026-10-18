import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\LiveController::index
* @see app/Http/Controllers/LiveController.php:36
* @route '//live.localhost'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//live.localhost',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LiveController::index
* @see app/Http/Controllers/LiveController.php:36
* @route '//live.localhost'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LiveController::index
* @see app/Http/Controllers/LiveController.php:36
* @route '//live.localhost'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LiveController::index
* @see app/Http/Controllers/LiveController.php:36
* @route '//live.localhost'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

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
* @see \App\Http\Controllers\LiveController::eventDqs
* @see app/Http/Controllers/LiveController.php:102
* @route '//live.localhost/dqs/{event}'
*/
export const eventDqs = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: eventDqs.url(args, options),
    method: 'get',
})

eventDqs.definition = {
    methods: ["get","head"],
    url: '//live.localhost/dqs/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LiveController::eventDqs
* @see app/Http/Controllers/LiveController.php:102
* @route '//live.localhost/dqs/{event}'
*/
eventDqs.url = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

    if (Array.isArray(args)) {
        args = {
            event: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: args.event,
    }

    return eventDqs.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LiveController::eventDqs
* @see app/Http/Controllers/LiveController.php:102
* @route '//live.localhost/dqs/{event}'
*/
eventDqs.get = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: eventDqs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LiveController::eventDqs
* @see app/Http/Controllers/LiveController.php:102
* @route '//live.localhost/dqs/{event}'
*/
eventDqs.head = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: eventDqs.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\LiveController::liveData
* @see app/Http/Controllers/LiveController.php:52
* @route '//live.localhost/{comp}'
*/
export const liveData = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: liveData.url(args, options),
    method: 'get',
})

liveData.definition = {
    methods: ["get","head"],
    url: '//live.localhost/{comp}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LiveController::liveData
* @see app/Http/Controllers/LiveController.php:52
* @route '//live.localhost/{comp}'
*/
liveData.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return liveData.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LiveController::liveData
* @see app/Http/Controllers/LiveController.php:52
* @route '//live.localhost/{comp}'
*/
liveData.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: liveData.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LiveController::liveData
* @see app/Http/Controllers/LiveController.php:52
* @route '//live.localhost/{comp}'
*/
liveData.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: liveData.url(args, options),
    method: 'head',
})

const LiveController = { index, all, dqs, eventDqs, liveData }

export default LiveController