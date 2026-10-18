import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\LiveController::event
* @see app/Http/Controllers/LiveController.php:102
* @route '//live.localhost/dqs/{event}'
*/
export const event = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: event.url(args, options),
    method: 'get',
})

event.definition = {
    methods: ["get","head"],
    url: '//live.localhost/dqs/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LiveController::event
* @see app/Http/Controllers/LiveController.php:102
* @route '//live.localhost/dqs/{event}'
*/
event.url = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return event.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\LiveController::event
* @see app/Http/Controllers/LiveController.php:102
* @route '//live.localhost/dqs/{event}'
*/
event.get = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: event.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LiveController::event
* @see app/Http/Controllers/LiveController.php:102
* @route '//live.localhost/dqs/{event}'
*/
event.head = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: event.url(args, options),
    method: 'head',
})

const dqs = {
    event: Object.assign(event, event),
}

export default dqs