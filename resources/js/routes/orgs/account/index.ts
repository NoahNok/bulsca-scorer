import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Organisation\OrganisationController::remove
* @see app/Http/Controllers/Organisation/OrganisationController.php:202
* @route '/organisation/{organisation}/accounts/remove'
*/
export const remove = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: remove.url(args, options),
    method: 'delete',
})

remove.definition = {
    methods: ["delete"],
    url: '/organisation/{organisation}/accounts/remove',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::remove
* @see app/Http/Controllers/Organisation/OrganisationController.php:202
* @route '/organisation/{organisation}/accounts/remove'
*/
remove.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return remove.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::remove
* @see app/Http/Controllers/Organisation/OrganisationController.php:202
* @route '/organisation/{organisation}/accounts/remove'
*/
remove.delete = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: remove.url(args, options),
    method: 'delete',
})

const account = {
    remove: Object.assign(remove, remove),
}

export default account