import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::login
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:16
* @route '//judge.localhost/v2/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::login
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:16
* @route '//judge.localhost/v2/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::login
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:16
* @route '//judge.localhost/v2/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::login
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:16
* @route '//judge.localhost/v2/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::loginPost
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:45
* @route '//judge.localhost/v2/login'
*/
export const loginPost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: loginPost.url(options),
    method: 'post',
})

loginPost.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::loginPost
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:45
* @route '//judge.localhost/v2/login'
*/
loginPost.url = (options?: RouteQueryOptions) => {
    return loginPost.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::loginPost
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:45
* @route '//judge.localhost/v2/login'
*/
loginPost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: loginPost.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::loginPassword
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:108
* @route '//judge.localhost/v2/login/password'
*/
export const loginPassword = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: loginPassword.url(options),
    method: 'post',
})

loginPassword.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/login/password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::loginPassword
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:108
* @route '//judge.localhost/v2/login/password'
*/
loginPassword.url = (options?: RouteQueryOptions) => {
    return loginPassword.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::loginPassword
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:108
* @route '//judge.localhost/v2/login/password'
*/
loginPassword.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: loginPassword.url(options),
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

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::logout
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:141
* @route '//judge.localhost/v2/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::logout
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:141
* @route '//judge.localhost/v2/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::logout
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:141
* @route '//judge.localhost/v2/logout'
*/
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

const LoginController = { login, loginPost, loginPassword, resendPin, logout }

export default LoginController