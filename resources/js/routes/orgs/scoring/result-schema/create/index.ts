import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Organisation\OrganisationController::post
* @see app/Http/Controllers/Organisation/OrganisationController.php:478
* @route '/organisation/{organisation}/scoring/result-schemas/create'
*/
export const post = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/scoring/result-schemas/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::post
* @see app/Http/Controllers/Organisation/OrganisationController.php:478
* @route '/organisation/{organisation}/scoring/result-schemas/create'
*/
post.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return post.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::post
* @see app/Http/Controllers/Organisation/OrganisationController.php:478
* @route '/organisation/{organisation}/scoring/result-schemas/create'
*/
post.post = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

