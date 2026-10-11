import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::forgotPassword
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:21
* @route '//judge.localhost/v2/forgot-password'
*/
export const forgotPassword = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: forgotPassword.url(options),
    method: 'get',
})

forgotPassword.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/forgot-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::forgotPassword
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:21
* @route '//judge.localhost/v2/forgot-password'
*/
forgotPassword.url = (options?: RouteQueryOptions) => {
    return forgotPassword.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::forgotPassword
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:21
* @route '//judge.localhost/v2/forgot-password'
*/
forgotPassword.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: forgotPassword.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::forgotPassword
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:21
* @route '//judge.localhost/v2/forgot-password'
*/
forgotPassword.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: forgotPassword.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::forgotPasswordPost
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:28
* @route '//judge.localhost/v2/forgot-password'
*/
export const forgotPasswordPost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: forgotPasswordPost.url(options),
    method: 'post',
})

forgotPasswordPost.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/forgot-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::forgotPasswordPost
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:28
* @route '//judge.localhost/v2/forgot-password'
*/
forgotPasswordPost.url = (options?: RouteQueryOptions) => {
    return forgotPasswordPost.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::forgotPasswordPost
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:28
* @route '//judge.localhost/v2/forgot-password'
*/
forgotPasswordPost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: forgotPasswordPost.url(options),
    method: 'post',
})

const ForgotPasswordController = { forgotPassword, forgotPasswordPost }

export default ForgotPasswordController