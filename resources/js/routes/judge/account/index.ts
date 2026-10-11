import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
import devices95ecd0 from './devices'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::update
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:28
* @route '//judge.localhost/v2/account'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/account',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::update
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:28
* @route '//judge.localhost/v2/account'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::update
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:28
* @route '//judge.localhost/v2/account'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::password
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:41
* @route '//judge.localhost/v2/account/password'
*/
export const password = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: password.url(options),
    method: 'post',
})

password.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/account/password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::password
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:41
* @route '//judge.localhost/v2/account/password'
*/
password.url = (options?: RouteQueryOptions) => {
    return password.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::password
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:41
* @route '//judge.localhost/v2/account/password'
*/
password.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: password.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::devices
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:59
* @route '//judge.localhost/v2/account/devices'
*/
export const devices = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: devices.url(options),
    method: 'get',
})

devices.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/account/devices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::devices
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:59
* @route '//judge.localhost/v2/account/devices'
*/
devices.url = (options?: RouteQueryOptions) => {
    return devices.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::devices
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:59
* @route '//judge.localhost/v2/account/devices'
*/
devices.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: devices.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::devices
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:59
* @route '//judge.localhost/v2/account/devices'
*/
devices.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: devices.url(options),
    method: 'head',
})

const account = {
    update: Object.assign(update, update),
    password: Object.assign(password, password),
    devices: Object.assign(devices, devices95ecd0),
}

export default account