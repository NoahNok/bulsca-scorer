import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::destroy
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:66
* @route '//judge.localhost/v2/account/devices/{login}'
*/
export const destroy = (args: { login: string | number } | [login: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '//judge.localhost/v2/account/devices/{login}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::destroy
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:66
* @route '//judge.localhost/v2/account/devices/{login}'
*/
destroy.url = (args: { login: string | number } | [login: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{login}', parsedArgs.login.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::destroy
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:66
* @route '//judge.localhost/v2/account/devices/{login}'
*/
destroy.delete = (args: { login: string | number } | [login: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::destroyOthers
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:79
* @route '//judge.localhost/v2/account/devices'
*/
export const destroyOthers = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyOthers.url(options),
    method: 'delete',
})

destroyOthers.definition = {
    methods: ["delete"],
    url: '//judge.localhost/v2/account/devices',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::destroyOthers
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:79
* @route '//judge.localhost/v2/account/devices'
*/
destroyOthers.url = (options?: RouteQueryOptions) => {
    return destroyOthers.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::destroyOthers
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:79
* @route '//judge.localhost/v2/account/devices'
*/
destroyOthers.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyOthers.url(options),
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

const devices = {
    destroy: Object.assign(destroy, destroy),
    destroyOthers: Object.assign(destroyOthers, destroyOthers),
    signOutEverywhere: Object.assign(signOutEverywhere, signOutEverywhere),
}

export default devices