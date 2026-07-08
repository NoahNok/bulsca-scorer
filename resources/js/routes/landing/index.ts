import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import competition633808 from './competition'
/**
* @see \App\Http\Controllers\Landing\LandingController::competition
* @see app/Http/Controllers/Landing/LandingController.php:30
* @route '/competition/{comp}'
*/
export const competition = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: competition.url(args, options),
    method: 'get',
})

competition.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\LandingController::competition
* @see app/Http/Controllers/Landing/LandingController.php:30
* @route '/competition/{comp}'
*/
competition.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return competition.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\LandingController::competition
* @see app/Http/Controllers/Landing/LandingController.php:30
* @route '/competition/{comp}'
*/
competition.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: competition.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::competition
* @see app/Http/Controllers/Landing/LandingController.php:30
* @route '/competition/{comp}'
*/
competition.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: competition.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::organisation
* @see app/Http/Controllers/Landing/LandingController.php:25
* @route '/{organisation}'
*/
export const organisation = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: organisation.url(args, options),
    method: 'get',
})

organisation.definition = {
    methods: ["get","head"],
    url: '/{organisation}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\LandingController::organisation
* @see app/Http/Controllers/Landing/LandingController.php:25
* @route '/{organisation}'
*/
organisation.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return organisation.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\LandingController::organisation
* @see app/Http/Controllers/Landing/LandingController.php:25
* @route '/{organisation}'
*/
organisation.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: organisation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::organisation
* @see app/Http/Controllers/Landing/LandingController.php:25
* @route '/{organisation}'
*/
organisation.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: organisation.url(args, options),
    method: 'head',
})

const landing = {
    competition: Object.assign(competition, competition633808),
    organisation: Object.assign(organisation, organisation),
}

export default landing