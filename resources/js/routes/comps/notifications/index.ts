import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Push\PushController::userSettings
* @see app/Http/Controllers/Push/PushController.php:35
* @route '/comps/{comp}/notifications'
*/
export const userSettings = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: userSettings.url(args, options),
    method: 'get',
})

userSettings.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Push\PushController::userSettings
* @see app/Http/Controllers/Push/PushController.php:35
* @route '/comps/{comp}/notifications'
*/
userSettings.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { comp: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
    }

    return userSettings.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Push\PushController::userSettings
* @see app/Http/Controllers/Push/PushController.php:35
* @route '/comps/{comp}/notifications'
*/
userSettings.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: userSettings.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Push\PushController::userSettings
* @see app/Http/Controllers/Push/PushController.php:35
* @route '/comps/{comp}/notifications'
*/
userSettings.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: userSettings.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Push\PushController::pushStore
* @see app/Http/Controllers/Push/PushController.php:15
* @route '/comps/{comp}/notifications/push'
*/
export const pushStore = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pushStore.url(args, options),
    method: 'post',
})

pushStore.definition = {
    methods: ["post"],
    url: '/comps/{comp}/notifications/push',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Push\PushController::pushStore
* @see app/Http/Controllers/Push/PushController.php:15
* @route '/comps/{comp}/notifications/push'
*/
pushStore.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { comp: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
    }

    return pushStore.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Push\PushController::pushStore
* @see app/Http/Controllers/Push/PushController.php:15
* @route '/comps/{comp}/notifications/push'
*/
pushStore.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: pushStore.url(args, options),
    method: 'post',
})

const notifications = {
    userSettings: Object.assign(userSettings, userSettings),
    pushStore: Object.assign(pushStore, pushStore),
}

export default notifications