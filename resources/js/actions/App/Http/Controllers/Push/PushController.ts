import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Push\PushController::userSettingsPage
* @see app/Http/Controllers/Push/PushController.php:35
* @route '/comps/{comp}/notifications'
*/
export const userSettingsPage = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: userSettingsPage.url(args, options),
    method: 'get',
})

userSettingsPage.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/notifications',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Push\PushController::userSettingsPage
* @see app/Http/Controllers/Push/PushController.php:35
* @route '/comps/{comp}/notifications'
*/
userSettingsPage.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return userSettingsPage.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Push\PushController::userSettingsPage
* @see app/Http/Controllers/Push/PushController.php:35
* @route '/comps/{comp}/notifications'
*/
userSettingsPage.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: userSettingsPage.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Push\PushController::userSettingsPage
* @see app/Http/Controllers/Push/PushController.php:35
* @route '/comps/{comp}/notifications'
*/
userSettingsPage.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: userSettingsPage.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Push\PushController::store
* @see app/Http/Controllers/Push/PushController.php:15
* @route '/comps/{comp}/notifications/push'
*/
export const store = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/comps/{comp}/notifications/push',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Push\PushController::store
* @see app/Http/Controllers/Push/PushController.php:15
* @route '/comps/{comp}/notifications/push'
*/
store.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Push\PushController::store
* @see app/Http/Controllers/Push/PushController.php:15
* @route '/comps/{comp}/notifications/push'
*/
store.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

const PushController = { userSettingsPage, store }

export default PushController