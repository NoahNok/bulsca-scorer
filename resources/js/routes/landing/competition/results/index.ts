import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
import breakdown from './breakdown'
import notes from './notes'
/**
* @see \App\Http\Controllers\Landing\ResultsController::get
* @see app/Http/Controllers/Landing/ResultsController.php:25
* @route '/competition/{comp}/results/{league}/{event}-{type}'
*/
export const get = (args: { comp: number | { id: number }, league: string | number, event: string | number, type: string | number } | [comp: number | { id: number }, league: string | number, event: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: get.url(args, options),
    method: 'get',
})

get.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results/{league}/{event}-{type}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\ResultsController::get
* @see app/Http/Controllers/Landing/ResultsController.php:25
* @route '/competition/{comp}/results/{league}/{event}-{type}'
*/
get.url = (args: { comp: number | { id: number }, league: string | number, event: string | number, type: string | number } | [comp: number | { id: number }, league: string | number, event: string | number, type: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            league: args[1],
            event: args[2],
            type: args[3],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        league: args.league,
        event: args.event,
        type: args.type,
    }

    return get.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{league}', parsedArgs.league.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace('{type}', parsedArgs.type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\ResultsController::get
* @see app/Http/Controllers/Landing/ResultsController.php:25
* @route '/competition/{comp}/results/{league}/{event}-{type}'
*/
get.get = (args: { comp: number | { id: number }, league: string | number, event: string | number, type: string | number } | [comp: number | { id: number }, league: string | number, event: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: get.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::get
* @see app/Http/Controllers/Landing/ResultsController.php:25
* @route '/competition/{comp}/results/{league}/{event}-{type}'
*/
get.head = (args: { comp: number | { id: number }, league: string | number, event: string | number, type: string | number } | [comp: number | { id: number }, league: string | number, event: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: get.url(args, options),
    method: 'head',
})

const results = {
    breakdown: Object.assign(breakdown, breakdown),
    notes: Object.assign(notes, notes),
    get: Object.assign(get, get),
}

export default results