import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::post
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:35
* @route '//judge.localhost/dq'
*/
export const post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '//judge.localhost/dq',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::post
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:35
* @route '//judge.localhost/dq'
*/
post.url = (options?: RouteQueryOptions) => {
    return post.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::post
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:35
* @route '//judge.localhost/dq'
*/
post.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

const index = {
    post: Object.assign(post, post),
}

export default index