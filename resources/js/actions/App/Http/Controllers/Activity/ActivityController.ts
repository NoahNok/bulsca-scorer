import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Activity\ActivityController::admin
* @see app/Http/Controllers/Activity/ActivityController.php:12
* @route '/admin/activity'
*/
export const admin = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: admin.url(options),
    method: 'get',
})

admin.definition = {
    methods: ["get","head"],
    url: '/admin/activity',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Activity\ActivityController::admin
* @see app/Http/Controllers/Activity/ActivityController.php:12
* @route '/admin/activity'
*/
admin.url = (options?: RouteQueryOptions) => {
    return admin.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Activity\ActivityController::admin
* @see app/Http/Controllers/Activity/ActivityController.php:12
* @route '/admin/activity'
*/
admin.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: admin.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Activity\ActivityController::admin
* @see app/Http/Controllers/Activity/ActivityController.php:12
* @route '/admin/activity'
*/
admin.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: admin.url(options),
    method: 'head',
})

const ActivityController = { admin }

export default ActivityController