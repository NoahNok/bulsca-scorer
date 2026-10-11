import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::post
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:22
* @route '//judge.localhost/v2/register'
*/
export const post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/register',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::post
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:22
* @route '//judge.localhost/v2/register'
*/
post.url = (options?: RouteQueryOptions) => {
    return post.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::post
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:22
* @route '//judge.localhost/v2/register'
*/
post.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

const register = {
    post: Object.assign(post, post),
}

export default register