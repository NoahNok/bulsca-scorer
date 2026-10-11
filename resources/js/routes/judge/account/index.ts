import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::update
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:24
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
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:24
* @route '//judge.localhost/v2/account'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::update
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:24
* @route '//judge.localhost/v2/account'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::password
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:37
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
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:37
* @route '//judge.localhost/v2/account/password'
*/
password.url = (options?: RouteQueryOptions) => {
    return password.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::password
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:37
* @route '//judge.localhost/v2/account/password'
*/
password.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: password.url(options),
    method: 'post',
})

const account = {
    update: Object.assign(update, update),
    password: Object.assign(password, password),
}

export default account