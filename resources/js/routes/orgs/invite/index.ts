import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Organisation\OrganisationController::cancel
* @see app/Http/Controllers/Organisation/OrganisationController.php:215
* @route '/organisation/{organisation}/invite/{inviteId}/cancel'
*/
export const cancel = (args: { organisation: number | { id: number }, inviteId: string | number } | [organisation: number | { id: number }, inviteId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cancel.url(args, options),
    method: 'get',
})

cancel.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/invite/{inviteId}/cancel',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::cancel
* @see app/Http/Controllers/Organisation/OrganisationController.php:215
* @route '/organisation/{organisation}/invite/{inviteId}/cancel'
*/
cancel.url = (args: { organisation: number | { id: number }, inviteId: string | number } | [organisation: number | { id: number }, inviteId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            inviteId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        inviteId: args.inviteId,
    }

    return cancel.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{inviteId}', parsedArgs.inviteId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::cancel
* @see app/Http/Controllers/Organisation/OrganisationController.php:215
* @route '/organisation/{organisation}/invite/{inviteId}/cancel'
*/
cancel.get = (args: { organisation: number | { id: number }, inviteId: string | number } | [organisation: number | { id: number }, inviteId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cancel.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::cancel
* @see app/Http/Controllers/Organisation/OrganisationController.php:215
* @route '/organisation/{organisation}/invite/{inviteId}/cancel'
*/
cancel.head = (args: { organisation: number | { id: number }, inviteId: string | number } | [organisation: number | { id: number }, inviteId: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: cancel.url(args, options),
    method: 'head',
})

const invite = {
    cancel: Object.assign(cancel, cancel),
}

export default invite