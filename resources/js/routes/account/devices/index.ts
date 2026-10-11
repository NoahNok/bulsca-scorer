import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AccountController::destroy
* @see app/Http/Controllers/AccountController.php:48
* @route '/account/devices/{login}'
*/
export const destroy = (args: { login: string | number } | [login: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/account/devices/{login}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AccountController::destroy
* @see app/Http/Controllers/AccountController.php:48
* @route '/account/devices/{login}'
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
* @see \App\Http\Controllers\AccountController::destroy
* @see app/Http/Controllers/AccountController.php:48
* @route '/account/devices/{login}'
*/
destroy.delete = (args: { login: string | number } | [login: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\AccountController::destroyOthers
* @see app/Http/Controllers/AccountController.php:57
* @route '/account/devices'
*/
export const destroyOthers = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyOthers.url(options),
    method: 'delete',
})

destroyOthers.definition = {
    methods: ["delete"],
    url: '/account/devices',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AccountController::destroyOthers
* @see app/Http/Controllers/AccountController.php:57
* @route '/account/devices'
*/
destroyOthers.url = (options?: RouteQueryOptions) => {
    return destroyOthers.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::destroyOthers
* @see app/Http/Controllers/AccountController.php:57
* @route '/account/devices'
*/
destroyOthers.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroyOthers.url(options),
    method: 'delete',
})

const devices = {
    destroy: Object.assign(destroy, destroy),
    destroyOthers: Object.assign(destroyOthers, destroyOthers),
}

export default devices