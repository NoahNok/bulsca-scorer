import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SpeedsEventController::add
* @see app/Http/Controllers/SpeedsEventController.php:18
* @route '/comps/{comp}/events/speeds/add'
*/
export const add = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: add.url(args, options),
    method: 'get',
})

add.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/speeds/add',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::add
* @see app/Http/Controllers/SpeedsEventController.php:18
* @route '/comps/{comp}/events/speeds/add'
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
* @see \App\Http\Controllers\SpeedsEventController::add
* @see app/Http/Controllers/SpeedsEventController.php:18
* @route '/comps/{comp}/events/speeds/add'
*/
add.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: add.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::add
* @see app/Http/Controllers/SpeedsEventController.php:18
* @route '/comps/{comp}/events/speeds/add'
*/
add.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: add.url(args, options),
    method: 'head',
})

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
* @see \App\Http\Controllers\SpeedsEventController::view
* @see app/Http/Controllers/SpeedsEventController.php:64
* @route '/comps/{comp}/events/speeds/{event}'
*/
export const view = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/speeds/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::view
* @see app/Http/Controllers/SpeedsEventController.php:64
* @route '/comps/{comp}/events/speeds/{event}'
*/
view.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return view.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpeedsEventController::view
* @see app/Http/Controllers/SpeedsEventController.php:64
* @route '/comps/{comp}/events/speeds/{event}'
*/
view.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::view
* @see app/Http/Controllers/SpeedsEventController.php:64
* @route '/comps/{comp}/events/speeds/{event}'
*/
view.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::editResult
* @see app/Http/Controllers/SpeedsEventController.php:124
* @route '/comps/{comp}/events/speeds/{event}/edit-result'
*/
export const editResult = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editResult.url(args, options),
    method: 'get',
})

editResult.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/speeds/{event}/edit-result',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::editResult
* @see app/Http/Controllers/SpeedsEventController.php:124
* @route '/comps/{comp}/events/speeds/{event}/edit-result'
*/
editResult.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return editResult.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpeedsEventController::editResult
* @see app/Http/Controllers/SpeedsEventController.php:124
* @route '/comps/{comp}/events/speeds/{event}/edit-result'
*/
editResult.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editResult.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::editResult
* @see app/Http/Controllers/SpeedsEventController.php:124
* @route '/comps/{comp}/events/speeds/{event}/edit-result'
*/
editResult.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: editResult.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::edit
* @see app/Http/Controllers/SpeedsEventController.php:81
* @route '/comps/{comp}/events/speeds/{event}/edit'
*/
export const edit = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/speeds/{event}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::edit
* @see app/Http/Controllers/SpeedsEventController.php:81
* @route '/comps/{comp}/events/speeds/{event}/edit'
*/
edit.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return edit.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpeedsEventController::edit
* @see app/Http/Controllers/SpeedsEventController.php:81
* @route '/comps/{comp}/events/speeds/{event}/edit'
*/
edit.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::edit
* @see app/Http/Controllers/SpeedsEventController.php:81
* @route '/comps/{comp}/events/speeds/{event}/edit'
*/
edit.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::editPost
* @see app/Http/Controllers/SpeedsEventController.php:87
* @route '/comps/{comp}/events/speeds/{event}/edit'
*/
export const editPost = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editPost.url(args, options),
    method: 'post',
})

editPost.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/speeds/{event}/edit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::editPost
* @see app/Http/Controllers/SpeedsEventController.php:87
* @route '/comps/{comp}/events/speeds/{event}/edit'
*/
editPost.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return editPost.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpeedsEventController::editPost
* @see app/Http/Controllers/SpeedsEventController.php:87
* @route '/comps/{comp}/events/speeds/{event}/edit'
*/
editPost.post = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::updateResults
* @see app/Http/Controllers/SpeedsEventController.php:129
* @route '/comps/{comp}/events/speeds/{event}/edit-result'
*/
export const updateResults = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateResults.url(args, options),
    method: 'post',
})

updateResults.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/speeds/{event}/edit-result',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::updateResults
* @see app/Http/Controllers/SpeedsEventController.php:129
* @route '/comps/{comp}/events/speeds/{event}/edit-result'
*/
updateResults.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return updateResults.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpeedsEventController::updateResults
* @see app/Http/Controllers/SpeedsEventController.php:129
* @route '/comps/{comp}/events/speeds/{event}/edit-result'
*/
updateResults.post = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateResults.url(args, options),
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

/**
* @see \App\Http\Controllers\SpeedsEventController::hide
* @see app/Http/Controllers/SpeedsEventController.php:299
* @route '/comps/{comp}/events/speeds/{event}/hide'
*/
export const hide = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

hide.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/speeds/{event}/hide',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::hide
* @see app/Http/Controllers/SpeedsEventController.php:299
* @route '/comps/{comp}/events/speeds/{event}/hide'
*/
hide.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return hide.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpeedsEventController::hide
* @see app/Http/Controllers/SpeedsEventController.php:299
* @route '/comps/{comp}/events/speeds/{event}/hide'
*/
hide.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::hide
* @see app/Http/Controllers/SpeedsEventController.php:299
* @route '/comps/{comp}/events/speeds/{event}/hide'
*/
hide.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: hide.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::scoringSettings
* @see app/Http/Controllers/SpeedsEventController.php:306
* @route '/comps/{comp}/events/speeds/{event}/scoring-settings'
*/
export const scoringSettings = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scoringSettings.url(args, options),
    method: 'get',
})

scoringSettings.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/speeds/{event}/scoring-settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::scoringSettings
* @see app/Http/Controllers/SpeedsEventController.php:306
* @route '/comps/{comp}/events/speeds/{event}/scoring-settings'
*/
scoringSettings.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return scoringSettings.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpeedsEventController::scoringSettings
* @see app/Http/Controllers/SpeedsEventController.php:306
* @route '/comps/{comp}/events/speeds/{event}/scoring-settings'
*/
scoringSettings.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scoringSettings.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::scoringSettings
* @see app/Http/Controllers/SpeedsEventController.php:306
* @route '/comps/{comp}/events/speeds/{event}/scoring-settings'
*/
scoringSettings.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: scoringSettings.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SpeedsEventController::saveScoringSettings
* @see app/Http/Controllers/SpeedsEventController.php:313
* @route '/comps/{comp}/events/speeds/{event}/scoring-settings'
*/
export const saveScoringSettings = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveScoringSettings.url(args, options),
    method: 'post',
})

saveScoringSettings.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/speeds/{event}/scoring-settings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SpeedsEventController::saveScoringSettings
* @see app/Http/Controllers/SpeedsEventController.php:313
* @route '/comps/{comp}/events/speeds/{event}/scoring-settings'
*/
saveScoringSettings.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return saveScoringSettings.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SpeedsEventController::saveScoringSettings
* @see app/Http/Controllers/SpeedsEventController.php:313
* @route '/comps/{comp}/events/speeds/{event}/scoring-settings'
*/
saveScoringSettings.post = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveScoringSettings.url(args, options),
    method: 'post',
})

const SpeedsEventController = { add, addPost, deleteMethod, view, editResult, edit, editPost, updateResults, printResults, hide, scoringSettings, saveScoringSettings, delete: deleteMethod }

export default SpeedsEventController