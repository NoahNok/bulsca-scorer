import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::register
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:16
* @route '//judge.localhost/v2/register'
*/
export const register = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

register.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/register',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::register
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:16
* @route '//judge.localhost/v2/register'
*/
register.url = (options?: RouteQueryOptions) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::register
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:16
* @route '//judge.localhost/v2/register'
*/
register.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::register
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:16
* @route '//judge.localhost/v2/register'
*/
register.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: register.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::registerPost
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:21
* @route '//judge.localhost/v2/register'
*/
export const registerPost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: registerPost.url(options),
    method: 'post',
})

registerPost.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/register',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::registerPost
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:21
* @route '//judge.localhost/v2/register'
*/
registerPost.url = (options?: RouteQueryOptions) => {
    return registerPost.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::registerPost
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:21
* @route '//judge.localhost/v2/register'
*/
registerPost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: registerPost.url(options),
    method: 'post',
})

const RegisterController = { register, registerPost }

export default RegisterController