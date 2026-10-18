import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import create4f58d6 from './create'
import edit055014 from './edit'
/**
* @see \App\Http\Controllers\AdminController::create
* @see app/Http/Controllers/AdminController.php:153
* @route '/admin/season/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/season/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::create
* @see app/Http/Controllers/AdminController.php:153
* @route '/admin/season/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::create
* @see app/Http/Controllers/AdminController.php:153
* @route '/admin/season/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::create
* @see app/Http/Controllers/AdminController.php:153
* @route '/admin/season/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::edit
* @see app/Http/Controllers/AdminController.php:172
* @route '/admin/season/edit/{season}'
*/
export const edit = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/season/edit/{season}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::edit
* @see app/Http/Controllers/AdminController.php:172
* @route '/admin/season/edit/{season}'
*/
edit.url = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { season: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { season: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            season: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        season: typeof args.season === 'object'
        ? args.season.id
        : args.season,
    }

    return edit.definition.url
            .replace('{season}', parsedArgs.season.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::edit
* @see app/Http/Controllers/AdminController.php:172
* @route '/admin/season/edit/{season}'
*/
edit.get = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::edit
* @see app/Http/Controllers/AdminController.php:172
* @route '/admin/season/edit/{season}'
*/
edit.head = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

const seasons = {
    create: Object.assign(create, create4f58d6),
    edit: Object.assign(edit, edit055014),
}

export default seasons