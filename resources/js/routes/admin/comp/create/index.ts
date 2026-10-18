import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminController::post
* @see app/Http/Controllers/AdminController.php:30
* @route '/admin/competition/create'
*/
export const post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '/admin/competition/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::post
* @see app/Http/Controllers/AdminController.php:30
* @route '/admin/competition/create'
*/
post.url = (options?: RouteQueryOptions) => {
    return post.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::post
* @see app/Http/Controllers/AdminController.php:30
* @route '/admin/competition/create'
*/
post.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

const create = {
    post: Object.assign(post, post),
}

export default create