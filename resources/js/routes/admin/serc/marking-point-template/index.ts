import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::index
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:14
* @route '/admin/serc/marking-points'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/serc/marking-points',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::index
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:14
* @route '/admin/serc/marking-points'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::index
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:14
* @route '/admin/serc/marking-points'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::index
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:14
* @route '/admin/serc/marking-points'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::create
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:26
* @route '/admin/serc/marking-points/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/serc/marking-points/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::create
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:26
* @route '/admin/serc/marking-points/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::create
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:26
* @route '/admin/serc/marking-points/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::create
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:26
* @route '/admin/serc/marking-points/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::store
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:34
* @route '/admin/serc/marking-points'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/serc/marking-points',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::store
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:34
* @route '/admin/serc/marking-points'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::store
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:34
* @route '/admin/serc/marking-points'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::edit
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:53
* @route '/admin/serc/marking-points/{marking_point}/edit'
*/
export const edit = (args: { marking_point: string | { id: string } } | [marking_point: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/serc/marking-points/{marking_point}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::edit
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:53
* @route '/admin/serc/marking-points/{marking_point}/edit'
*/
edit.url = (args: { marking_point: string | { id: string } } | [marking_point: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { marking_point: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { marking_point: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            marking_point: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        marking_point: typeof args.marking_point === 'object'
        ? args.marking_point.id
        : args.marking_point,
    }

    return edit.definition.url
            .replace('{marking_point}', parsedArgs.marking_point.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::edit
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:53
* @route '/admin/serc/marking-points/{marking_point}/edit'
*/
edit.get = (args: { marking_point: string | { id: string } } | [marking_point: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::edit
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:53
* @route '/admin/serc/marking-points/{marking_point}/edit'
*/
edit.head = (args: { marking_point: string | { id: string } } | [marking_point: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::update
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:63
* @route '/admin/serc/marking-points/{marking_point}'
*/
export const update = (args: { marking_point: string | { id: string } } | [marking_point: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/serc/marking-points/{marking_point}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::update
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:63
* @route '/admin/serc/marking-points/{marking_point}'
*/
update.url = (args: { marking_point: string | { id: string } } | [marking_point: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { marking_point: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { marking_point: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            marking_point: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        marking_point: typeof args.marking_point === 'object'
        ? args.marking_point.id
        : args.marking_point,
    }

    return update.definition.url
            .replace('{marking_point}', parsedArgs.marking_point.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::update
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:63
* @route '/admin/serc/marking-points/{marking_point}'
*/
update.put = (args: { marking_point: string | { id: string } } | [marking_point: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::update
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:63
* @route '/admin/serc/marking-points/{marking_point}'
*/
update.patch = (args: { marking_point: string | { id: string } } | [marking_point: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::destroy
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:80
* @route '/admin/serc/marking-points/{marking_point}'
*/
export const destroy = (args: { marking_point: string | { id: string } } | [marking_point: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/serc/marking-points/{marking_point}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::destroy
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:80
* @route '/admin/serc/marking-points/{marking_point}'
*/
destroy.url = (args: { marking_point: string | { id: string } } | [marking_point: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { marking_point: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { marking_point: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            marking_point: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        marking_point: typeof args.marking_point === 'object'
        ? args.marking_point.id
        : args.marking_point,
    }

    return destroy.definition.url
            .replace('{marking_point}', parsedArgs.marking_point.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERC\MarkingPointTemplateController::destroy
* @see app/Http/Controllers/SERC/MarkingPointTemplateController.php:80
* @route '/admin/serc/marking-points/{marking_point}'
*/
destroy.delete = (args: { marking_point: string | { id: string } } | [marking_point: string | { id: string } ] | string | { id: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const markingPointTemplate = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default markingPointTemplate