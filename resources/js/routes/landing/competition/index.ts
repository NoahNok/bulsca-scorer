import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import results8ded7a from './results'
/**
* @see \App\Http\Controllers\Landing\LandingController::heatsDraws
* @see app/Http/Controllers/Landing/LandingController.php:35
* @route '/competition/{comp}/heats-and-draws'
*/
export const heatsDraws = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heatsDraws.url(args, options),
    method: 'get',
})

heatsDraws.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/heats-and-draws',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\LandingController::heatsDraws
* @see app/Http/Controllers/Landing/LandingController.php:35
* @route '/competition/{comp}/heats-and-draws'
*/
heatsDraws.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return heatsDraws.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\LandingController::heatsDraws
* @see app/Http/Controllers/Landing/LandingController.php:35
* @route '/competition/{comp}/heats-and-draws'
*/
heatsDraws.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heatsDraws.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::heatsDraws
* @see app/Http/Controllers/Landing/LandingController.php:35
* @route '/competition/{comp}/heats-and-draws'
*/
heatsDraws.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: heatsDraws.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::results
* @see app/Http/Controllers/Landing/LandingController.php:40
* @route '/competition/{comp}/results'
*/
export const results = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

results.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\LandingController::results
* @see app/Http/Controllers/Landing/LandingController.php:40
* @route '/competition/{comp}/results'
*/
results.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return results.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\LandingController::results
* @see app/Http/Controllers/Landing/LandingController.php:40
* @route '/competition/{comp}/results'
*/
results.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::results
* @see app/Http/Controllers/Landing/LandingController.php:40
* @route '/competition/{comp}/results'
*/
results.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: results.url(args, options),
    method: 'head',
})

const competition = {
    heatsDraws: Object.assign(heatsDraws, heatsDraws),
    results: Object.assign(results, results8ded7a),
}

export default competition