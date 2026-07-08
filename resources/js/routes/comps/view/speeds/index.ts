import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SpeedsEventController::hide
* @see app/Http/Controllers/SpeedsEventController.php:299
* @route '/comps/{comp}/events/speeds/{event}/hide'
*/
export const hide = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

hide.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/speeds/{event}/hide',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::hide
* @see app/Http/Controllers/SpeedsEventController.php:299
* @route '/comps/{comp}/events/speeds/{event}/hide'
*/
hide.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            event: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return hide.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpeedsEventController::hide
* @see app/Http/Controllers/SpeedsEventController.php:299
* @route '/comps/{comp}/events/speeds/{event}/hide'
*/
hide.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::hide
* @see app/Http/Controllers/SpeedsEventController.php:299
* @route '/comps/{comp}/events/speeds/{event}/hide'
*/
hide.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: hide.url(args, options),
    method: 'head',
})

const speeds = {
    hide: Object.assign(hide, hide),
}

export default speeds