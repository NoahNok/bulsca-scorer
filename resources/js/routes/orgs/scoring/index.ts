import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import resultSchema from './result-schema'
import edit055014 from './edit'
/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:236
* @route '/organisation/{organisation}/scoring/create'
*/
export const create = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/scoring/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:236
* @route '/organisation/{organisation}/scoring/create'
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
* @see app/Http/Controllers/Organisation/OrganisationController.php:236
* @route '/organisation/{organisation}/scoring/create'
*/
create.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:236
* @route '/organisation/{organisation}/scoring/create'
*/
create.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:255
* @route '/organisation/{organisation}/scoring/{schema}/edit'
*/
export const edit = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/scoring/{schema}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:255
* @route '/organisation/{organisation}/scoring/{schema}/edit'
*/
edit.url = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return edit.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:255
* @route '/organisation/{organisation}/scoring/{schema}/edit'
*/
edit.get = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:255
* @route '/organisation/{organisation}/scoring/{schema}/edit'
*/
edit.head = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteMethod
* @see app/Http/Controllers/Organisation/OrganisationController.php:281
* @route '/organisation/{organisation}/scoring/{schema}'
*/
export const deleteMethod = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/organisation/{organisation}/scoring/{schema}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteMethod
* @see app/Http/Controllers/Organisation/OrganisationController.php:281
* @route '/organisation/{organisation}/scoring/{schema}'
*/
deleteMethod.url = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return deleteMethod.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteMethod
* @see app/Http/Controllers/Organisation/OrganisationController.php:281
* @route '/organisation/{organisation}/scoring/{schema}'
*/
deleteMethod.delete = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

const scoring = {
    resultSchema: Object.assign(resultSchema, resultSchema),
    create: Object.assign(create, create),
    edit: Object.assign(edit, edit055014),
    delete: Object.assign(deleteMethod, deleteMethod),
}

export default scoring