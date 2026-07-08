import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import create4f58d6 from './create'
import update from './update'
/**
* @see \App\Http\Controllers\AdminController::create
* @see app/Http/Controllers/AdminController.php:24
* @route '/admin/competition/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/competition/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::create
* @see app/Http/Controllers/AdminController.php:24
* @route '/admin/competition/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::create
* @see app/Http/Controllers/AdminController.php:24
* @route '/admin/competition/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::create
* @see app/Http/Controllers/AdminController.php:24
* @route '/admin/competition/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::view
* @see app/Http/Controllers/AdminController.php:81
* @route '/admin/competition/{comp}'
*/
export const view = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/admin/competition/{comp}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::view
* @see app/Http/Controllers/AdminController.php:81
* @route '/admin/competition/{comp}'
*/
view.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return view.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::view
* @see app/Http/Controllers/AdminController.php:81
* @route '/admin/competition/{comp}'
*/
view.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::view
* @see app/Http/Controllers/AdminController.php:81
* @route '/admin/competition/{comp}'
*/
view.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::deleteMethod
* @see app/Http/Controllers/AdminController.php:132
* @route '/admin/competition/{comp}/delete'
*/
export const deleteMethod = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/admin/competition/{comp}/delete',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::deleteMethod
* @see app/Http/Controllers/AdminController.php:132
* @route '/admin/competition/{comp}/delete'
*/
deleteMethod.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return deleteMethod.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::deleteMethod
* @see app/Http/Controllers/AdminController.php:132
* @route '/admin/competition/{comp}/delete'
*/
deleteMethod.delete = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

const comp = {
    create: Object.assign(create, create4f58d6),
    view: Object.assign(view, view),
    update: Object.assign(update, update),
    delete: Object.assign(deleteMethod, deleteMethod),
}

export default comp