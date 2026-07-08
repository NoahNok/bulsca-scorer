import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import resolve36a22e from './resolve'
/**
* @see \App\Http\Controllers\AccountInviteController::show
* @see app/Http/Controllers/AccountInviteController.php:21
* @route '/invite/{invite}/{email}'
*/
export const show = (args: { invite: string | { id: string }, email: string | number } | [invite: string | { id: string }, email: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/invite/{invite}/{email}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AccountInviteController::show
* @see app/Http/Controllers/AccountInviteController.php:21
* @route '/invite/{invite}/{email}'
*/
show.url = (args: { invite: string | { id: string }, email: string | number } | [invite: string | { id: string }, email: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            invite: args[0],
            email: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        invite: typeof args.invite === 'object'
        ? args.invite.id
        : args.invite,
        email: args.email,
    }

    return show.definition.url
            .replace('{invite}', parsedArgs.invite.toString())
            .replace('{email}', parsedArgs.email.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountInviteController::show
* @see app/Http/Controllers/AccountInviteController.php:21
* @route '/invite/{invite}/{email}'
*/
show.get = (args: { invite: string | { id: string }, email: string | number } | [invite: string | { id: string }, email: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountInviteController::show
* @see app/Http/Controllers/AccountInviteController.php:21
* @route '/invite/{invite}/{email}'
*/
show.head = (args: { invite: string | { id: string }, email: string | number } | [invite: string | { id: string }, email: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AccountInviteController::resolve
* @see app/Http/Controllers/AccountInviteController.php:52
* @route '/invite/{invite}/{email}/resolve/{resolution}'
*/
export const resolve = (args: { invite: string | { id: string }, email: string | number, resolution: string | number } | [invite: string | { id: string }, email: string | number, resolution: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resolve.url(args, options),
    method: 'get',
})

resolve.definition = {
    methods: ["get","head"],
    url: '/invite/{invite}/{email}/resolve/{resolution}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AccountInviteController::resolve
* @see app/Http/Controllers/AccountInviteController.php:52
* @route '/invite/{invite}/{email}/resolve/{resolution}'
*/
resolve.url = (args: { invite: string | { id: string }, email: string | number, resolution: string | number } | [invite: string | { id: string }, email: string | number, resolution: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            invite: args[0],
            email: args[1],
            resolution: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        invite: typeof args.invite === 'object'
        ? args.invite.id
        : args.invite,
        email: args.email,
        resolution: args.resolution,
    }

    return resolve.definition.url
            .replace('{invite}', parsedArgs.invite.toString())
            .replace('{email}', parsedArgs.email.toString())
            .replace('{resolution}', parsedArgs.resolution.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountInviteController::resolve
* @see app/Http/Controllers/AccountInviteController.php:52
* @route '/invite/{invite}/{email}/resolve/{resolution}'
*/
resolve.get = (args: { invite: string | { id: string }, email: string | number, resolution: string | number } | [invite: string | { id: string }, email: string | number, resolution: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resolve.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountInviteController::resolve
* @see app/Http/Controllers/AccountInviteController.php:52
* @route '/invite/{invite}/{email}/resolve/{resolution}'
*/
resolve.head = (args: { invite: string | { id: string }, email: string | number, resolution: string | number } | [invite: string | { id: string }, email: string | number, resolution: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: resolve.url(args, options),
    method: 'head',
})

const invite = {
    show: Object.assign(show, show),
    resolve: Object.assign(resolve, resolve36a22e),
}

export default invite