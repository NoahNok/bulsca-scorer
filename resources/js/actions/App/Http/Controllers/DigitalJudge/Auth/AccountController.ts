import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::account
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:14
* @route '//judge.localhost/v2/account'
*/
export const account = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(options),
    method: 'get',
})

account.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/account',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::account
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:14
* @route '//judge.localhost/v2/account'
*/
account.url = (options?: RouteQueryOptions) => {
    return account.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::account
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:14
* @route '//judge.localhost/v2/account'
*/
account.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::account
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:14
* @route '//judge.localhost/v2/account'
*/
account.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: account.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::updateAccount
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:24
* @route '//judge.localhost/v2/account'
*/
export const updateAccount = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateAccount.url(options),
    method: 'post',
})

updateAccount.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/account',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::updateAccount
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:24
* @route '//judge.localhost/v2/account'
*/
updateAccount.url = (options?: RouteQueryOptions) => {
    return updateAccount.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::updateAccount
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:24
* @route '//judge.localhost/v2/account'
*/
updateAccount.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateAccount.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::updatePassword
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:37
* @route '//judge.localhost/v2/account/password'
*/
export const updatePassword = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updatePassword.url(options),
    method: 'post',
})

updatePassword.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/account/password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::updatePassword
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:37
* @route '//judge.localhost/v2/account/password'
*/
updatePassword.url = (options?: RouteQueryOptions) => {
    return updatePassword.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::updatePassword
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:37
* @route '//judge.localhost/v2/account/password'
*/
updatePassword.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updatePassword.url(options),
    method: 'post',
})

const AccountController = { account, updateAccount, updatePassword }

export default AccountController