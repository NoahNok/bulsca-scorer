import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
import forgot05cbb4 from './forgot'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::forgot
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:20
* @route '//judge.localhost/v2/forgot-password'
*/
export const forgot = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: forgot.url(options),
    method: 'get',
})

forgot.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/forgot-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::forgot
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:20
* @route '//judge.localhost/v2/forgot-password'
*/
forgot.url = (options?: RouteQueryOptions) => {
    return forgot.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::forgot
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:20
* @route '//judge.localhost/v2/forgot-password'
*/
forgot.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: forgot.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\ForgotPasswordController::forgot
* @see app/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController.php:20
* @route '//judge.localhost/v2/forgot-password'
*/
forgot.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: forgot.url(options),
    method: 'head',
})

const password = {
    forgot: Object.assign(forgot, forgot05cbb4),
}

export default password