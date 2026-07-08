import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\SpeedsEventController::addPost
* @see app/Http/Controllers/SpeedsEventController.php:23
* @route '/comps/{comp}/events/speeds/add'
*/
export const addPost = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addPost.url(args, options),
    method: 'post',
})

addPost.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/speeds/add',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::addPost
* @see app/Http/Controllers/SpeedsEventController.php:23
* @route '/comps/{comp}/events/speeds/add'
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
* @see \App\Http\Controllers\SpeedsEventController::addPost
* @see app/Http/Controllers/SpeedsEventController.php:23
* @route '/comps/{comp}/events/speeds/add'
*/
addPost.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::deleteMethod
* @see app/Http/Controllers/SpeedsEventController.php:286
* @route '/comps/{comp}/events/speeds/{event}/delete'
*/
export const deleteMethod = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/comps/{comp}/events/speeds/{event}/delete',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::deleteMethod
* @see app/Http/Controllers/SpeedsEventController.php:286
* @route '/comps/{comp}/events/speeds/{event}/delete'
*/
deleteMethod.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            event: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return deleteMethod.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpeedsEventController::deleteMethod
* @see app/Http/Controllers/SpeedsEventController.php:286
* @route '/comps/{comp}/events/speeds/{event}/delete'
*/
deleteMethod.delete = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::editResultPost
* @see app/Http/Controllers/SpeedsEventController.php:129
* @route '/comps/{comp}/events/speeds/{event}/edit-result'
*/
export const editResultPost = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editResultPost.url(args, options),
    method: 'post',
})

editResultPost.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/speeds/{event}/edit-result',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::editResultPost
* @see app/Http/Controllers/SpeedsEventController.php:129
* @route '/comps/{comp}/events/speeds/{event}/edit-result'
*/
editResultPost.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            event: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return editResultPost.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpeedsEventController::editResultPost
* @see app/Http/Controllers/SpeedsEventController.php:129
* @route '/comps/{comp}/events/speeds/{event}/edit-result'
*/
editResultPost.post = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editResultPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::printResults
* @see app/Http/Controllers/SpeedsEventController.php:333
* @route '/comps/{comp}/events/speeds/{event}/print-results'
*/
export const printResults = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printResults.url(args, options),
    method: 'get',
})

printResults.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/speeds/{event}/print-results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::printResults
* @see app/Http/Controllers/SpeedsEventController.php:333
* @route '/comps/{comp}/events/speeds/{event}/print-results'
*/
printResults.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            event: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return printResults.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpeedsEventController::printResults
* @see app/Http/Controllers/SpeedsEventController.php:333
* @route '/comps/{comp}/events/speeds/{event}/print-results'
*/
printResults.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::printResults
* @see app/Http/Controllers/SpeedsEventController.php:333
* @route '/comps/{comp}/events/speeds/{event}/print-results'
*/
printResults.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: printResults.url(args, options),
    method: 'head',
})

const speeds = {
    addPost: Object.assign(addPost, addPost),
    delete: Object.assign(deleteMethod, deleteMethod),
    editResultPost: Object.assign(editResultPost, editResultPost),
    printResults: Object.assign(printResults, printResults),
}

export default speeds