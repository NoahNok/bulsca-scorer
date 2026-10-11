import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AccountController::search
* @see app/Http/Controllers/AccountController.php:13
* @route '/accounts/search/{email}'
*/
export const search = (args: { email: string | number } | [email: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(args, options),
    method: 'get',
})

search.definition = {
    methods: ["get","head"],
    url: '/accounts/search/{email}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AccountController::search
* @see app/Http/Controllers/AccountController.php:13
* @route '/accounts/search/{email}'
*/
search.url = (args: { email: string | number } | [email: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { email: args }
    }

    if (Array.isArray(args)) {
        args = {
            email: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        email: args.email,
    }

    return search.definition.url
            .replace('{email}', parsedArgs.email.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::search
* @see app/Http/Controllers/AccountController.php:13
* @route '/accounts/search/{email}'
*/
search.get = (args: { email: string | number } | [email: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountController::search
* @see app/Http/Controllers/AccountController.php:13
* @route '/accounts/search/{email}'
*/
search.head = (args: { email: string | number } | [email: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: search.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AccountController::edit
* @see app/Http/Controllers/AccountController.php:29
* @route '/account'
*/
export const edit = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/account',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AccountController::edit
* @see app/Http/Controllers/AccountController.php:29
* @route '/account'
*/
edit.url = (options?: RouteQueryOptions) => {
    return edit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::edit
* @see app/Http/Controllers/AccountController.php:29
* @route '/account'
*/
edit.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountController::edit
* @see app/Http/Controllers/AccountController.php:29
* @route '/account'
*/
edit.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AccountController::update
* @see app/Http/Controllers/AccountController.php:37
* @route '/account'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/account',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AccountController::update
* @see app/Http/Controllers/AccountController.php:37
* @route '/account'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::update
* @see app/Http/Controllers/AccountController.php:37
* @route '/account'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AccountController::signOutDevice
* @see app/Http/Controllers/AccountController.php:48
* @route '/account/devices/{login}'
*/
export const signOutDevice = (args: { login: string | number } | [login: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: signOutDevice.url(args, options),
    method: 'delete',
})

signOutDevice.definition = {
    methods: ["delete"],
    url: '/account/devices/{login}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AccountController::signOutDevice
* @see app/Http/Controllers/AccountController.php:48
* @route '/account/devices/{login}'
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
* @see \App\Http\Controllers\AccountController::signOutDevice
* @see app/Http/Controllers/AccountController.php:48
* @route '/account/devices/{login}'
*/
signOutDevice.delete = (args: { login: string | number } | [login: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: signOutDevice.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\AccountController::signOutOtherDevices
* @see app/Http/Controllers/AccountController.php:57
* @route '/account/devices'
*/
export const signOutOtherDevices = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: signOutOtherDevices.url(options),
    method: 'delete',
})

signOutOtherDevices.definition = {
    methods: ["delete"],
    url: '/account/devices',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AccountController::signOutOtherDevices
* @see app/Http/Controllers/AccountController.php:57
* @route '/account/devices'
*/
signOutOtherDevices.url = (options?: RouteQueryOptions) => {
    return signOutOtherDevices.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::signOutOtherDevices
* @see app/Http/Controllers/AccountController.php:57
* @route '/account/devices'
*/
signOutOtherDevices.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: signOutOtherDevices.url(options),
    method: 'delete',
})

const AccountController = { search, edit, update, signOutDevice, signOutOtherDevices }

export default AccountController