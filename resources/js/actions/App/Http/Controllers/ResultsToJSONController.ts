import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ResultsToJSONController::viewSerc
* @see app/Http/Controllers/ResultsToJSONController.php:11
* @route '/api/results/{comp_slug}/serc/{event}'
*/
export const viewSerc = (args: { comp_slug: number | { id: number }, event: number | { id: number } } | [comp_slug: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSerc.url(args, options),
    method: 'get',
})

viewSerc.definition = {
    methods: ["get","head"],
    url: '/api/results/{comp_slug}/serc/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ResultsToJSONController::viewSerc
* @see app/Http/Controllers/ResultsToJSONController.php:11
* @route '/api/results/{comp_slug}/serc/{event}'
*/
viewSerc.url = (args: { comp_slug: number | { id: number }, event: number | { id: number } } | [comp_slug: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp_slug: args[0],
            event: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp_slug: typeof args.comp_slug === 'object'
        ? args.comp_slug.id
        : args.comp_slug,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return viewSerc.definition.url
            .replace('{comp_slug}', parsedArgs.comp_slug.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ResultsToJSONController::viewSerc
* @see app/Http/Controllers/ResultsToJSONController.php:11
* @route '/api/results/{comp_slug}/serc/{event}'
*/
viewSerc.get = (args: { comp_slug: number | { id: number }, event: number | { id: number } } | [comp_slug: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSerc.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ResultsToJSONController::viewSerc
* @see app/Http/Controllers/ResultsToJSONController.php:11
* @route '/api/results/{comp_slug}/serc/{event}'
*/
viewSerc.head = (args: { comp_slug: number | { id: number }, event: number | { id: number } } | [comp_slug: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewSerc.url(args, options),
    method: 'head',
})

const ResultsToJSONController = { viewSerc }

export default ResultsToJSONController