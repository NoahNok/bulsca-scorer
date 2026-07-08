import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Organisation\OrganisationController::post
* @see app/Http/Controllers/Organisation/OrganisationController.php:503
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}/edit'
*/
export const post = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/scoring/result-schemas/{schema}/edit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::post
* @see app/Http/Controllers/Organisation/OrganisationController.php:503
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}/edit'
*/
post.url = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return post.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::post
* @see app/Http/Controllers/Organisation/OrganisationController.php:503
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}/edit'
*/
post.post = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

const edit = {
    post: Object.assign(post, post),
}

export default edit