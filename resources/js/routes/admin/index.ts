import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import comp from './comp'
import recordsE88d67 from './records'
import seasons4b53d6 from './seasons'
import serc from './serc'
/**
* @see \App\Http\Controllers\AdminController::index
* @see app/Http/Controllers/AdminController.php:19
* @route '/admin'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::index
* @see app/Http/Controllers/AdminController.php:19
* @route '/admin'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::index
* @see app/Http/Controllers/AdminController.php:19
* @route '/admin'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::index
* @see app/Http/Controllers/AdminController.php:19
* @route '/admin'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::records
* @see app/Http/Controllers/AdminController.php:102
* @route '/admin/records'
*/
export const records = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: records.url(options),
    method: 'get',
})

records.definition = {
    methods: ["get","head"],
    url: '/admin/records',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::records
* @see app/Http/Controllers/AdminController.php:102
* @route '/admin/records'
*/
records.url = (options?: RouteQueryOptions) => {
    return records.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::records
* @see app/Http/Controllers/AdminController.php:102
* @route '/admin/records'
*/
records.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: records.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::records
* @see app/Http/Controllers/AdminController.php:102
* @route '/admin/records'
*/
records.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: records.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::seasons
* @see app/Http/Controllers/AdminController.php:148
* @route '/admin/seasons'
*/
export const seasons = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: seasons.url(options),
    method: 'get',
})

seasons.definition = {
    methods: ["get","head"],
    url: '/admin/seasons',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::seasons
* @see app/Http/Controllers/AdminController.php:148
* @route '/admin/seasons'
*/
seasons.url = (options?: RouteQueryOptions) => {
    return seasons.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::seasons
* @see app/Http/Controllers/AdminController.php:148
* @route '/admin/seasons'
*/
seasons.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: seasons.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::seasons
* @see app/Http/Controllers/AdminController.php:148
* @route '/admin/seasons'
*/
seasons.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: seasons.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Activity\ActivityController::activity
* @see app/Http/Controllers/Activity/ActivityController.php:12
* @route '/admin/activity'
*/
export const activity = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activity.url(options),
    method: 'get',
})

activity.definition = {
    methods: ["get","head"],
    url: '/admin/activity',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Activity\ActivityController::activity
* @see app/Http/Controllers/Activity/ActivityController.php:12
* @route '/admin/activity'
*/
activity.url = (options?: RouteQueryOptions) => {
    return activity.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Activity\ActivityController::activity
* @see app/Http/Controllers/Activity/ActivityController.php:12
* @route '/admin/activity'
*/
activity.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activity.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Activity\ActivityController::activity
* @see app/Http/Controllers/Activity/ActivityController.php:12
* @route '/admin/activity'
*/
activity.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: activity.url(options),
    method: 'head',
})

const admin = {
    index: Object.assign(index, index),
    comp: Object.assign(comp, comp),
    records: Object.assign(records, recordsE88d67),
    seasons: Object.assign(seasons, seasons4b53d6),
    activity: Object.assign(activity, activity),
    serc: Object.assign(serc, serc),
}

export default admin