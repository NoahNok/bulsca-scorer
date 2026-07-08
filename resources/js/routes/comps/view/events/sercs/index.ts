import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\SERCController::printResults
* @see app/Http/Controllers/SERCController.php:358
* @route '/comps/{comp}/events/sercs/{serc}/print-results'
*/
export const printResults = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printResults.url(args, options),
    method: 'get',
})

printResults.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/print-results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::printResults
* @see app/Http/Controllers/SERCController.php:358
* @route '/comps/{comp}/events/sercs/{serc}/print-results'
*/
printResults.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return printResults.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::printResults
* @see app/Http/Controllers/SERCController.php:358
* @route '/comps/{comp}/events/sercs/{serc}/print-results'
*/
printResults.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::printResults
* @see app/Http/Controllers/SERCController.php:358
* @route '/comps/{comp}/events/sercs/{serc}/print-results'
*/
printResults.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: printResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERCController::deleteMethod
* @see app/Http/Controllers/SERCController.php:123
* @route '/comps/{comp}/events/sercs/{serc}'
*/
export const deleteMethod = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/comps/{comp}/events/sercs/{serc}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\SERCController::deleteMethod
* @see app/Http/Controllers/SERCController.php:123
* @route '/comps/{comp}/events/sercs/{serc}'
*/
deleteMethod.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return deleteMethod.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::deleteMethod
* @see app/Http/Controllers/SERCController.php:123
* @route '/comps/{comp}/events/sercs/{serc}'
*/
deleteMethod.delete = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\SERCController::next
* @see app/Http/Controllers/SERCController.php:254
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/next'
*/
export const next = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: next.url(args, options),
    method: 'get',
})

next.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/next',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::next
* @see app/Http/Controllers/SERCController.php:254
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/next'
*/
next.url = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            serc: args[1],
            entity_id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
        entity_id: args.entity_id,
    }

    return next.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::next
* @see app/Http/Controllers/SERCController.php:254
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/next'
*/
next.get = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: next.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::next
* @see app/Http/Controllers/SERCController.php:254
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/next'
*/
next.head = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: next.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERCController::editResultsPost
* @see app/Http/Controllers/SERCController.php:140
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
export const editResultsPost = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editResultsPost.url(args, options),
    method: 'post',
})

editResultsPost.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SERCController::editResultsPost
* @see app/Http/Controllers/SERCController.php:140
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
editResultsPost.url = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            serc: args[1],
            entity_id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
        entity_id: args.entity_id,
    }

    return editResultsPost.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::editResultsPost
* @see app/Http/Controllers/SERCController.php:140
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
editResultsPost.post = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editResultsPost.url(args, options),
    method: 'post',
})

const sercs = {
    printResults: Object.assign(printResults, printResults),
    delete: Object.assign(deleteMethod, deleteMethod),
    next: Object.assign(next, next),
    editResultsPost: Object.assign(editResultsPost, editResultsPost),
}

export default sercs