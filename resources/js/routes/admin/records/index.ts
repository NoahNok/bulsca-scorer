import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminController::update
* @see app/Http/Controllers/AdminController.php:108
* @route '/admin/records'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/admin/records',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::update
* @see app/Http/Controllers/AdminController.php:108
* @route '/admin/records'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::update
* @see app/Http/Controllers/AdminController.php:108
* @route '/admin/records'
*/
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

const records = {
    update: Object.assign(update, update),
}

export default records