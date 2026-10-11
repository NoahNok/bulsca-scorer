import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::account
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:17
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
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:17
* @route '//judge.localhost/v2/account'
*/
account.url = (options?: RouteQueryOptions) => {
    return account.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::account
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:17
* @route '//judge.localhost/v2/account'
*/
account.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::account
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:17
* @route '//judge.localhost/v2/account'
*/
account.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: account.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::updateAccount
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:28
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
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:28
* @route '//judge.localhost/v2/account'
*/
updateAccount.url = (options?: RouteQueryOptions) => {
    return updateAccount.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::updateAccount
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:28
* @route '//judge.localhost/v2/account'
*/
updateAccount.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateAccount.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::updatePassword
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:41
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
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:41
* @route '//judge.localhost/v2/account/password'
*/
updatePassword.url = (options?: RouteQueryOptions) => {
    return updatePassword.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::updatePassword
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:41
* @route '//judge.localhost/v2/account/password'
*/
updatePassword.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updatePassword.url(options),
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

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::signOutDevice
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:66
* @route '//judge.localhost/v2/account/devices/{login}'
*/
export const signOutDevice = (args: { login: string | number } | [login: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: signOutDevice.url(args, options),
    method: 'delete',
})

signOutDevice.definition = {
    methods: ["delete"],
    url: '//judge.localhost/v2/account/devices/{login}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::signOutDevice
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:66
* @route '//judge.localhost/v2/account/devices/{login}'
*/
signOutDevice.url = (args: { login: string | number } | [login: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { login: args }
    }

    if (Array.isArray(args)) {
        args = {
            login: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        login: args.login,
    }

    return signOutDevice.definition.url
            .replace('{login}', parsedArgs.login.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::signOutDevice
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:66
* @route '//judge.localhost/v2/account/devices/{login}'
*/
signOutDevice.delete = (args: { login: string | number } | [login: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: signOutDevice.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::signOutOtherDevices
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:79
* @route '//judge.localhost/v2/account/devices'
*/
export const signOutOtherDevices = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: signOutOtherDevices.url(options),
    method: 'delete',
})

signOutOtherDevices.definition = {
    methods: ["delete"],
    url: '//judge.localhost/v2/account/devices',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::signOutOtherDevices
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:79
* @route '//judge.localhost/v2/account/devices'
*/
signOutOtherDevices.url = (options?: RouteQueryOptions) => {
    return signOutOtherDevices.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::signOutOtherDevices
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:79
* @route '//judge.localhost/v2/account/devices'
*/
signOutOtherDevices.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: signOutOtherDevices.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::signOutEverywhere
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:88
* @route '//judge.localhost/v2/account/devices/sign-out-everywhere'
*/
export const signOutEverywhere = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: signOutEverywhere.url(options),
    method: 'post',
})

signOutEverywhere.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/account/devices/sign-out-everywhere',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::signOutEverywhere
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:88
* @route '//judge.localhost/v2/account/devices/sign-out-everywhere'
*/
signOutEverywhere.url = (options?: RouteQueryOptions) => {
    return signOutEverywhere.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::signOutEverywhere
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:88
* @route '//judge.localhost/v2/account/devices/sign-out-everywhere'
*/
signOutEverywhere.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: signOutEverywhere.url(options),
    method: 'post',
})

const AccountController = { account, updateAccount, updatePassword, devices, signOutDevice, signOutOtherDevices, signOutEverywhere }

export default AccountController