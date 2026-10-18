import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminController::post
* @see app/Http/Controllers/AdminController.php:177
* @route '/admin/season/edit/{season}'
*/
export const post = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '/admin/season/edit/{season}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::post
* @see app/Http/Controllers/AdminController.php:177
* @route '/admin/season/edit/{season}'
*/
post.url = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { season: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { season: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            season: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        season: typeof args.season === 'object'
        ? args.season.id
        : args.season,
    }

    return post.definition.url
            .replace('{season}', parsedArgs.season.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::post
* @see app/Http/Controllers/AdminController.php:177
* @route '/admin/season/edit/{season}'
*/
post.post = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

const edit = {
    post: Object.assign(post, post),
}

export default edit