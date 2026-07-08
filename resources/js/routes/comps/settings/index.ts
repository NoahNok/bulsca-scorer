import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\CompetitionController::scoring
* @see app/Http/Controllers/CompetitionController.php:129
* @route '/comps/{comp}/settings/scoring'
*/
export const scoring = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: scoring.url(args, options),
    method: 'post',
})

scoring.definition = {
    methods: ["post"],
    url: '/comps/{comp}/settings/scoring',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::scoring
* @see app/Http/Controllers/CompetitionController.php:129
* @route '/comps/{comp}/settings/scoring'
*/
scoring.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return scoring.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::scoring
* @see app/Http/Controllers/CompetitionController.php:129
* @route '/comps/{comp}/settings/scoring'
*/
scoring.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: scoring.url(args, options),
    method: 'post',
})

const settings = {
    scoring: Object.assign(scoring, scoring),
}

export default settings