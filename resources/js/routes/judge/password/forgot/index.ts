import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::post
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:27
* @route '//judge.localhost/v2/forgot-password'
*/
export const post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/forgot-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::post
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:27
* @route '//judge.localhost/v2/forgot-password'
*/
post.url = (options?: RouteQueryOptions) => {
    return post.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::post
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:27
* @route '//judge.localhost/v2/forgot-password'
*/
post.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

const forgot = {
    post: Object.assign(post, post),
}

export default forgot