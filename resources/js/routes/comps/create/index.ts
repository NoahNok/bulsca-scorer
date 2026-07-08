import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\CompetitionController::post
* @see app/Http/Controllers/CompetitionController.php:276
* @route '/create'
*/
export const post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::post
* @see app/Http/Controllers/CompetitionController.php:276
* @route '/create'
*/
post.url = (options?: RouteQueryOptions) => {
    return post.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::post
* @see app/Http/Controllers/CompetitionController.php:276
* @route '/create'
*/
post.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

const create = {
    post: Object.assign(post, post),
}

export default create