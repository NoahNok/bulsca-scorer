import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Landing\LandingController::explore
* @see app/Http/Controllers/Landing/LandingController.php:14
* @route '/explore'
*/
export const explore = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: explore.url(options),
    method: 'get',
})

explore.definition = {
    methods: ["get","head"],
    url: '/explore',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\LandingController::explore
* @see app/Http/Controllers/Landing/LandingController.php:14
* @route '/explore'
*/
explore.url = (options?: RouteQueryOptions) => {
    return explore.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\LandingController::explore
* @see app/Http/Controllers/Landing/LandingController.php:14
* @route '/explore'
*/
explore.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: explore.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::explore
* @see app/Http/Controllers/Landing/LandingController.php:14
* @route '/explore'
*/
explore.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: explore.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::search
* @see app/Http/Controllers/Landing/LandingController.php:45
* @route '/search/{search}'
*/
export const search = (args: { search: string | number } | [search: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(args, options),
    method: 'get',
})

search.definition = {
    methods: ["get","head"],
    url: '/search/{search}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\LandingController::search
* @see app/Http/Controllers/Landing/LandingController.php:45
* @route '/search/{search}'
*/
search.url = (args: { search: string | number } | [search: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { search: args }
    }

    if (Array.isArray(args)) {
        args = {
            search: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        search: args.search,
    }

    return search.definition.url
            .replace('{search}', parsedArgs.search.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\LandingController::search
* @see app/Http/Controllers/Landing/LandingController.php:45
* @route '/search/{search}'
*/
search.get = (args: { search: string | number } | [search: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::search
* @see app/Http/Controllers/Landing/LandingController.php:45
* @route '/search/{search}'
*/
search.head = (args: { search: string | number } | [search: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: search.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::showCompetition
* @see app/Http/Controllers/Landing/LandingController.php:30
* @route '/competition/{comp}'
*/
export const showCompetition = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showCompetition.url(args, options),
    method: 'get',
})

showCompetition.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\LandingController::showCompetition
* @see app/Http/Controllers/Landing/LandingController.php:30
* @route '/competition/{comp}'
*/
showCompetition.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return showCompetition.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\LandingController::showCompetition
* @see app/Http/Controllers/Landing/LandingController.php:30
* @route '/competition/{comp}'
*/
showCompetition.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showCompetition.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::showCompetition
* @see app/Http/Controllers/Landing/LandingController.php:30
* @route '/competition/{comp}'
*/
showCompetition.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showCompetition.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::showHeatsAndDraws
* @see app/Http/Controllers/Landing/LandingController.php:35
* @route '/competition/{comp}/heats-and-draws'
*/
export const showHeatsAndDraws = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showHeatsAndDraws.url(args, options),
    method: 'get',
})

showHeatsAndDraws.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/heats-and-draws',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\LandingController::showHeatsAndDraws
* @see app/Http/Controllers/Landing/LandingController.php:35
* @route '/competition/{comp}/heats-and-draws'
*/
showHeatsAndDraws.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return showHeatsAndDraws.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\LandingController::showHeatsAndDraws
* @see app/Http/Controllers/Landing/LandingController.php:35
* @route '/competition/{comp}/heats-and-draws'
*/
showHeatsAndDraws.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showHeatsAndDraws.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::showHeatsAndDraws
* @see app/Http/Controllers/Landing/LandingController.php:35
* @route '/competition/{comp}/heats-and-draws'
*/
showHeatsAndDraws.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showHeatsAndDraws.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::showResults
* @see app/Http/Controllers/Landing/LandingController.php:40
* @route '/competition/{comp}/results'
*/
export const showResults = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showResults.url(args, options),
    method: 'get',
})

showResults.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\LandingController::showResults
* @see app/Http/Controllers/Landing/LandingController.php:40
* @route '/competition/{comp}/results'
*/
showResults.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return showResults.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\LandingController::showResults
* @see app/Http/Controllers/Landing/LandingController.php:40
* @route '/competition/{comp}/results'
*/
showResults.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::showResults
* @see app/Http/Controllers/Landing/LandingController.php:40
* @route '/competition/{comp}/results'
*/
showResults.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::showOrganisation
* @see app/Http/Controllers/Landing/LandingController.php:25
* @route '/{organisation}'
*/
export const showOrganisation = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showOrganisation.url(args, options),
    method: 'get',
})

showOrganisation.definition = {
    methods: ["get","head"],
    url: '/{organisation}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\LandingController::showOrganisation
* @see app/Http/Controllers/Landing/LandingController.php:25
* @route '/{organisation}'
*/
showOrganisation.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return showOrganisation.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\LandingController::showOrganisation
* @see app/Http/Controllers/Landing/LandingController.php:25
* @route '/{organisation}'
*/
showOrganisation.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showOrganisation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::showOrganisation
* @see app/Http/Controllers/Landing/LandingController.php:25
* @route '/{organisation}'
*/
showOrganisation.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showOrganisation.url(args, options),
    method: 'head',
})

const LandingController = { explore, search, showCompetition, showHeatsAndDraws, showResults, showOrganisation }

export default LandingController