import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import devices from './devices'
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

const account = {
    update: Object.assign(update, update),
    devices: Object.assign(devices, devices),
}

export default account