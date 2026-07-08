import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Result\MasterSchemaController::add
* @see app/Http/Controllers/Result/MasterSchemaController.php:15
* @route '/comps/{comp}/results/master/add'
*/
export const add = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: add.url(args, options),
    method: 'get',
})

add.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/results/master/add',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::add
* @see app/Http/Controllers/Result/MasterSchemaController.php:15
* @route '/comps/{comp}/results/master/add'
*/
add.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return add.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::add
* @see app/Http/Controllers/Result/MasterSchemaController.php:15
* @route '/comps/{comp}/results/master/add'
*/
add.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: add.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::add
* @see app/Http/Controllers/Result/MasterSchemaController.php:15
* @route '/comps/{comp}/results/master/add'
*/
add.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: add.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::addPost
* @see app/Http/Controllers/Result/MasterSchemaController.php:20
* @route '/comps/{comp}/results/master/add'
*/
export const addPost = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addPost.url(args, options),
    method: 'post',
})

addPost.definition = {
    methods: ["post"],
    url: '/comps/{comp}/results/master/add',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::addPost
* @see app/Http/Controllers/Result/MasterSchemaController.php:20
* @route '/comps/{comp}/results/master/add'
*/
addPost.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return addPost.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::addPost
* @see app/Http/Controllers/Result/MasterSchemaController.php:20
* @route '/comps/{comp}/results/master/add'
*/
addPost.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::view
* @see app/Http/Controllers/Result/MasterSchemaController.php:52
* @route '/comps/{comp}/results/master/{schema}'
*/
export const view = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/results/master/{schema}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::view
* @see app/Http/Controllers/Result/MasterSchemaController.php:52
* @route '/comps/{comp}/results/master/{schema}'
*/
view.url = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return view.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::view
* @see app/Http/Controllers/Result/MasterSchemaController.php:52
* @route '/comps/{comp}/results/master/{schema}'
*/
view.get = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::view
* @see app/Http/Controllers/Result/MasterSchemaController.php:52
* @route '/comps/{comp}/results/master/{schema}'
*/
view.head = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::deleteMethod
* @see app/Http/Controllers/Result/MasterSchemaController.php:59
* @route '/comps/{comp}/results/master/{schema}'
*/
export const deleteMethod = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/comps/{comp}/results/master/{schema}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::deleteMethod
* @see app/Http/Controllers/Result/MasterSchemaController.php:59
* @route '/comps/{comp}/results/master/{schema}'
*/
deleteMethod.url = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return deleteMethod.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::deleteMethod
* @see app/Http/Controllers/Result/MasterSchemaController.php:59
* @route '/comps/{comp}/results/master/{schema}'
*/
deleteMethod.delete = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::hide
* @see app/Http/Controllers/Result/MasterSchemaController.php:65
* @route '/comps/{comp}/results/master/{schema}/hide'
*/
export const hide = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

hide.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/results/master/{schema}/hide',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::hide
* @see app/Http/Controllers/Result/MasterSchemaController.php:65
* @route '/comps/{comp}/results/master/{schema}/hide'
*/
hide.url = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return hide.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::hide
* @see app/Http/Controllers/Result/MasterSchemaController.php:65
* @route '/comps/{comp}/results/master/{schema}/hide'
*/
hide.get = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Result\MasterSchemaController::hide
* @see app/Http/Controllers/Result/MasterSchemaController.php:65
* @route '/comps/{comp}/results/master/{schema}/hide'
*/
hide.head = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: hide.url(args, options),
    method: 'head',
})

const MasterSchemaController = { add, addPost, view, deleteMethod, hide, delete: deleteMethod }

export default MasterSchemaController