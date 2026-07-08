import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:391
* @route '/organisation/{organisation}/infractions/create'
*/
export const create = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(args, options),
    method: 'post',
})

create.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/infractions/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:391
* @route '/organisation/{organisation}/infractions/create'
*/
create.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return create.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:391
* @route '/organisation/{organisation}/infractions/create'
*/
create.post = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: create.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::view
* @see app/Http/Controllers/Organisation/OrganisationController.php:304
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
export const view = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/infractions/{type}/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::view
* @see app/Http/Controllers/Organisation/OrganisationController.php:304
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
view.url = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            type: args[1],
            id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        type: args.type,
        id: args.id,
    }

    return view.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{type}', parsedArgs.type.toString())
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::view
* @see app/Http/Controllers/Organisation/OrganisationController.php:304
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
view.get = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::view
* @see app/Http/Controllers/Organisation/OrganisationController.php:304
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
view.head = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::update
* @see app/Http/Controllers/Organisation/OrganisationController.php:332
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
export const update = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/infractions/{type}/{id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::update
* @see app/Http/Controllers/Organisation/OrganisationController.php:332
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
update.url = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            type: args[1],
            id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        type: args.type,
        id: args.id,
    }

    return update.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{type}', parsedArgs.type.toString())
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::update
* @see app/Http/Controllers/Organisation/OrganisationController.php:332
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
update.post = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteMethod
* @see app/Http/Controllers/Organisation/OrganisationController.php:446
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
export const deleteMethod = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/organisation/{organisation}/infractions/{type}/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteMethod
* @see app/Http/Controllers/Organisation/OrganisationController.php:446
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
deleteMethod.url = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            type: args[1],
            id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        type: args.type,
        id: args.id,
    }

    return deleteMethod.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{type}', parsedArgs.type.toString())
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteMethod
* @see app/Http/Controllers/Organisation/OrganisationController.php:446
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
deleteMethod.delete = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

const infractions = {
    create: Object.assign(create, create),
    view: Object.assign(view, view),
    update: Object.assign(update, update),
    delete: Object.assign(deleteMethod, deleteMethod),
}

export default infractions