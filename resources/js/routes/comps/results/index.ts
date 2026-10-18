import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\OverallResultsController::add
* @see app/Http/Controllers/OverallResultsController.php:81
* @route '/comps/{comp}/results/add'
*/
export const add = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: add.url(args, options),
    method: 'get',
})

add.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/results/add',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OverallResultsController::add
* @see app/Http/Controllers/OverallResultsController.php:81
* @route '/comps/{comp}/results/add'
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
* @see \App\Http\Controllers\OverallResultsController::add
* @see app/Http/Controllers/OverallResultsController.php:81
* @route '/comps/{comp}/results/add'
*/
add.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: add.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OverallResultsController::add
* @see app/Http/Controllers/OverallResultsController.php:81
* @route '/comps/{comp}/results/add'
*/
add.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: add.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OverallResultsController::quickGen
* @see app/Http/Controllers/OverallResultsController.php:0
* @route '/comps/{comp}/results/qg'
*/
export const quickGen = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: quickGen.url(args, options),
    method: 'get',
})

quickGen.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/results/qg',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OverallResultsController::quickGen
* @see app/Http/Controllers/OverallResultsController.php:0
* @route '/comps/{comp}/results/qg'
*/
quickGen.url = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: args.comp,
    }

    return quickGen.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OverallResultsController::quickGen
* @see app/Http/Controllers/OverallResultsController.php:0
* @route '/comps/{comp}/results/qg'
*/
quickGen.get = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: quickGen.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OverallResultsController::quickGen
* @see app/Http/Controllers/OverallResultsController.php:0
* @route '/comps/{comp}/results/qg'
*/
quickGen.head = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: quickGen.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OverallResultsController::publishToggle
* @see app/Http/Controllers/OverallResultsController.php:191
* @route '/comps/{comp}/results/pt'
*/
export const publishToggle = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: publishToggle.url(args, options),
    method: 'get',
})

publishToggle.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/results/pt',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OverallResultsController::publishToggle
* @see app/Http/Controllers/OverallResultsController.php:191
* @route '/comps/{comp}/results/pt'
*/
publishToggle.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return publishToggle.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OverallResultsController::publishToggle
* @see app/Http/Controllers/OverallResultsController.php:191
* @route '/comps/{comp}/results/pt'
*/
publishToggle.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: publishToggle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OverallResultsController::publishToggle
* @see app/Http/Controllers/OverallResultsController.php:191
* @route '/comps/{comp}/results/pt'
*/
publishToggle.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: publishToggle.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OverallResultsController::provToggle
* @see app/Http/Controllers/OverallResultsController.php:199
* @route '/comps/{comp}/results/prt'
*/
export const provToggle = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: provToggle.url(args, options),
    method: 'get',
})

provToggle.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/results/prt',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OverallResultsController::provToggle
* @see app/Http/Controllers/OverallResultsController.php:199
* @route '/comps/{comp}/results/prt'
*/
provToggle.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return provToggle.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OverallResultsController::provToggle
* @see app/Http/Controllers/OverallResultsController.php:199
* @route '/comps/{comp}/results/prt'
*/
provToggle.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: provToggle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OverallResultsController::provToggle
* @see app/Http/Controllers/OverallResultsController.php:199
* @route '/comps/{comp}/results/prt'
*/
provToggle.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: provToggle.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OverallResultsController::addPost
* @see app/Http/Controllers/OverallResultsController.php:86
* @route '/comps/{comp}/results'
*/
export const addPost = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addPost.url(args, options),
    method: 'post',
})

addPost.definition = {
    methods: ["post"],
    url: '/comps/{comp}/results',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\OverallResultsController::addPost
* @see app/Http/Controllers/OverallResultsController.php:86
* @route '/comps/{comp}/results'
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
* @see \App\Http\Controllers\OverallResultsController::addPost
* @see app/Http/Controllers/OverallResultsController.php:86
* @route '/comps/{comp}/results'
*/
addPost.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\OverallResultsController::deleteMethod
* @see app/Http/Controllers/OverallResultsController.php:212
* @route '/comps/{comp}/results/{schema}'
*/
export const deleteMethod = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/comps/{comp}/results/{schema}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\OverallResultsController::deleteMethod
* @see app/Http/Controllers/OverallResultsController.php:212
* @route '/comps/{comp}/results/{schema}'
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
* @see \App\Http\Controllers\OverallResultsController::deleteMethod
* @see app/Http/Controllers/OverallResultsController.php:212
* @route '/comps/{comp}/results/{schema}'
*/
deleteMethod.delete = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\OverallResultsController::hide
* @see app/Http/Controllers/OverallResultsController.php:218
* @route '/comps/{comp}/results/{schema}/hide'
*/
export const hide = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

hide.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/results/{schema}/hide',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OverallResultsController::hide
* @see app/Http/Controllers/OverallResultsController.php:218
* @route '/comps/{comp}/results/{schema}/hide'
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
* @see \App\Http\Controllers\OverallResultsController::hide
* @see app/Http/Controllers/OverallResultsController.php:218
* @route '/comps/{comp}/results/{schema}/hide'
*/
hide.get = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OverallResultsController::hide
* @see app/Http/Controllers/OverallResultsController.php:218
* @route '/comps/{comp}/results/{schema}/hide'
*/
hide.head = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: hide.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OverallResultsController::printAll
* @see app/Http/Controllers/OverallResultsController.php:63
* @route '/comps/{comp}/results/print-all'
*/
export const printAll = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printAll.url(args, options),
    method: 'get',
})

printAll.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/results/print-all',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OverallResultsController::printAll
* @see app/Http/Controllers/OverallResultsController.php:63
* @route '/comps/{comp}/results/print-all'
*/
printAll.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return printAll.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OverallResultsController::printAll
* @see app/Http/Controllers/OverallResultsController.php:63
* @route '/comps/{comp}/results/print-all'
*/
printAll.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printAll.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OverallResultsController::printAll
* @see app/Http/Controllers/OverallResultsController.php:63
* @route '/comps/{comp}/results/print-all'
*/
printAll.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: printAll.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OverallResultsController::viewSchema
* @see app/Http/Controllers/OverallResultsController.php:20
* @route '/comp/results/view-schema/{schema}'
*/
export const viewSchema = (args: { schema: number | { id: number } } | [schema: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSchema.url(args, options),
    method: 'get',
})

viewSchema.definition = {
    methods: ["get","head"],
    url: '/comp/results/view-schema/{schema}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OverallResultsController::viewSchema
* @see app/Http/Controllers/OverallResultsController.php:20
* @route '/comp/results/view-schema/{schema}'
*/
viewSchema.url = (args: { schema: number | { id: number } } | [schema: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schema: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { schema: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            schema: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return viewSchema.definition.url
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OverallResultsController::viewSchema
* @see app/Http/Controllers/OverallResultsController.php:20
* @route '/comp/results/view-schema/{schema}'
*/
viewSchema.get = (args: { schema: number | { id: number } } | [schema: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSchema.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OverallResultsController::viewSchema
* @see app/Http/Controllers/OverallResultsController.php:20
* @route '/comp/results/view-schema/{schema}'
*/
viewSchema.head = (args: { schema: number | { id: number } } | [schema: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewSchema.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OverallResultsController::viewSchemaPrint
* @see app/Http/Controllers/OverallResultsController.php:55
* @route '/comp/results/view-schema/{schema}/print'
*/
export const viewSchemaPrint = (args: { schema: number | { id: number } } | [schema: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSchemaPrint.url(args, options),
    method: 'get',
})

viewSchemaPrint.definition = {
    methods: ["get","head"],
    url: '/comp/results/view-schema/{schema}/print',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OverallResultsController::viewSchemaPrint
* @see app/Http/Controllers/OverallResultsController.php:55
* @route '/comp/results/view-schema/{schema}/print'
*/
viewSchemaPrint.url = (args: { schema: number | { id: number } } | [schema: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schema: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { schema: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            schema: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return viewSchemaPrint.definition.url
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OverallResultsController::viewSchemaPrint
* @see app/Http/Controllers/OverallResultsController.php:55
* @route '/comp/results/view-schema/{schema}/print'
*/
viewSchemaPrint.get = (args: { schema: number | { id: number } } | [schema: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSchemaPrint.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OverallResultsController::viewSchemaPrint
* @see app/Http/Controllers/OverallResultsController.php:55
* @route '/comp/results/view-schema/{schema}/print'
*/
viewSchemaPrint.head = (args: { schema: number | { id: number } } | [schema: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewSchemaPrint.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OverallResultsController::viewSchemaPrintBasic
* @see app/Http/Controllers/OverallResultsController.php:47
* @route '/comp/results/view-schema/{schema}/print-basic'
*/
export const viewSchemaPrintBasic = (args: { schema: number | { id: number } } | [schema: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSchemaPrintBasic.url(args, options),
    method: 'get',
})

viewSchemaPrintBasic.definition = {
    methods: ["get","head"],
    url: '/comp/results/view-schema/{schema}/print-basic',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OverallResultsController::viewSchemaPrintBasic
* @see app/Http/Controllers/OverallResultsController.php:47
* @route '/comp/results/view-schema/{schema}/print-basic'
*/
viewSchemaPrintBasic.url = (args: { schema: number | { id: number } } | [schema: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schema: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { schema: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            schema: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return viewSchemaPrintBasic.definition.url
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OverallResultsController::viewSchemaPrintBasic
* @see app/Http/Controllers/OverallResultsController.php:47
* @route '/comp/results/view-schema/{schema}/print-basic'
*/
viewSchemaPrintBasic.get = (args: { schema: number | { id: number } } | [schema: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSchemaPrintBasic.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OverallResultsController::viewSchemaPrintBasic
* @see app/Http/Controllers/OverallResultsController.php:47
* @route '/comp/results/view-schema/{schema}/print-basic'
*/
viewSchemaPrintBasic.head = (args: { schema: number | { id: number } } | [schema: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewSchemaPrintBasic.url(args, options),
    method: 'head',
})

const results = {
    add: Object.assign(add, add),
    quickGen: Object.assign(quickGen, quickGen),
    publishToggle: Object.assign(publishToggle, publishToggle),
    provToggle: Object.assign(provToggle, provToggle),
    addPost: Object.assign(addPost, addPost),
    delete: Object.assign(deleteMethod, deleteMethod),
    hide: Object.assign(hide, hide),
    printAll: Object.assign(printAll, printAll),
    viewSchema: Object.assign(viewSchema, viewSchema),
    viewSchemaPrint: Object.assign(viewSchemaPrint, viewSchemaPrint),
    viewSchemaPrintBasic: Object.assign(viewSchemaPrintBasic, viewSchemaPrintBasic),
}

export default results