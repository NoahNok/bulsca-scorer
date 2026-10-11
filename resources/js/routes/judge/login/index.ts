import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::post
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:45
* @route '//judge.localhost/v2/login'
*/
export const post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::post
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:45
* @route '//judge.localhost/v2/login'
*/
post.url = (options?: RouteQueryOptions) => {
    return post.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::post
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:45
* @route '//judge.localhost/v2/login'
*/
post.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::password
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:108
* @route '//judge.localhost/v2/login/password'
*/
export const password = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: password.url(options),
    method: 'post',
})

password.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/login/password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::password
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:108
* @route '//judge.localhost/v2/login/password'
*/
password.url = (options?: RouteQueryOptions) => {
    return password.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::password
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:108
* @route '//judge.localhost/v2/login/password'
*/
password.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: password.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::resendPin
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:24
* @route '//judge.localhost/v2/login/resend-pin'
*/
export const resendPin = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendPin.url(options),
    method: 'post',
})

resendPin.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/login/resend-pin',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::resendPin
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:24
* @route '//judge.localhost/v2/login/resend-pin'
*/
resendPin.url = (options?: RouteQueryOptions) => {
    return resendPin.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::resendPin
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:24
* @route '//judge.localhost/v2/login/resend-pin'
*/
resendPin.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendPin.url(options),
    method: 'post',
})

const login = {
    post: Object.assign(post, post),
    password: Object.assign(password, password),
    resendPin: Object.assign(resendPin, resendPin),
}

export default login