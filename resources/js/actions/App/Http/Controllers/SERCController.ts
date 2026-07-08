import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SERCController::add
* @see app/Http/Controllers/SERCController.php:28
* @route '/comps/{comp}/events/sercs/add'
*/
export const add = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: add.url(args, options),
    method: 'get',
})

add.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/add',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::add
* @see app/Http/Controllers/SERCController.php:28
* @route '/comps/{comp}/events/sercs/add'
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
* @see \App\Http\Controllers\SERCController::add
* @see app/Http/Controllers/SERCController.php:28
* @route '/comps/{comp}/events/sercs/add'
*/
add.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: add.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::add
* @see app/Http/Controllers/SERCController.php:28
* @route '/comps/{comp}/events/sercs/add'
*/
add.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: add.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERCController::addPost
* @see app/Http/Controllers/SERCController.php:33
* @route '/comps/{comp}/events/sercs/add'
*/
export const addPost = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addPost.url(args, options),
    method: 'post',
})

addPost.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/sercs/add',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SERCController::addPost
* @see app/Http/Controllers/SERCController.php:33
* @route '/comps/{comp}/events/sercs/add'
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
* @see \App\Http\Controllers\SERCController::addPost
* @see app/Http/Controllers/SERCController.php:33
* @route '/comps/{comp}/events/sercs/add'
*/
addPost.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SERCController::view
* @see app/Http/Controllers/SERCController.php:41
* @route '/comps/{comp}/events/sercs/{serc}'
*/
export const view = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::view
* @see app/Http/Controllers/SERCController.php:41
* @route '/comps/{comp}/events/sercs/{serc}'
*/
view.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return view.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::view
* @see app/Http/Controllers/SERCController.php:41
* @route '/comps/{comp}/events/sercs/{serc}'
*/
view.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::view
* @see app/Http/Controllers/SERCController.php:41
* @route '/comps/{comp}/events/sercs/{serc}'
*/
view.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERCController::edit
* @see app/Http/Controllers/SERCController.php:63
* @route '/comps/{comp}/events/sercs/{serc}/edit'
*/
export const edit = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::edit
* @see app/Http/Controllers/SERCController.php:63
* @route '/comps/{comp}/events/sercs/{serc}/edit'
*/
edit.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return edit.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::edit
* @see app/Http/Controllers/SERCController.php:63
* @route '/comps/{comp}/events/sercs/{serc}/edit'
*/
edit.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::edit
* @see app/Http/Controllers/SERCController.php:63
* @route '/comps/{comp}/events/sercs/{serc}/edit'
*/
edit.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERCController::editPost
* @see app/Http/Controllers/SERCController.php:68
* @route '/comps/{comp}/events/sercs/{serc}/edit'
*/
export const editPost = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editPost.url(args, options),
    method: 'post',
})

editPost.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/sercs/{serc}/edit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SERCController::editPost
* @see app/Http/Controllers/SERCController.php:68
* @route '/comps/{comp}/events/sercs/{serc}/edit'
*/
editPost.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return editPost.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::editPost
* @see app/Http/Controllers/SERCController.php:68
* @route '/comps/{comp}/events/sercs/{serc}/edit'
*/
editPost.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editPost.url(args, options),
    method: 'post',
})

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
* @see \App\Http\Controllers\SERCController::editResultsView
* @see app/Http/Controllers/SERCController.php:132
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
export const editResultsView = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editResultsView.url(args, options),
    method: 'get',
})

editResultsView.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::editResultsView
* @see app/Http/Controllers/SERCController.php:132
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
editResultsView.url = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
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

    return editResultsView.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::editResultsView
* @see app/Http/Controllers/SERCController.php:132
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
editResultsView.get = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editResultsView.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::editResultsView
* @see app/Http/Controllers/SERCController.php:132
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
editResultsView.head = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: editResultsView.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERCController::updateTeamResults
* @see app/Http/Controllers/SERCController.php:140
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
export const updateTeamResults = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateTeamResults.url(args, options),
    method: 'post',
})

updateTeamResults.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SERCController::updateTeamResults
* @see app/Http/Controllers/SERCController.php:140
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
updateTeamResults.url = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
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

    return updateTeamResults.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::updateTeamResults
* @see app/Http/Controllers/SERCController.php:140
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
updateTeamResults.post = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateTeamResults.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SERCController::hide
* @see app/Http/Controllers/SERCController.php:265
* @route '/comps/{comp}/events/sercs/{serc}/hide'
*/
export const hide = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

hide.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/hide',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::hide
* @see app/Http/Controllers/SERCController.php:265
* @route '/comps/{comp}/events/sercs/{serc}/hide'
*/
hide.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return hide.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::hide
* @see app/Http/Controllers/SERCController.php:265
* @route '/comps/{comp}/events/sercs/{serc}/hide'
*/
hide.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::hide
* @see app/Http/Controllers/SERCController.php:265
* @route '/comps/{comp}/events/sercs/{serc}/hide'
*/
hide.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: hide.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERCController::addSercImage
* @see app/Http/Controllers/SERCController.php:271
* @route '/comps/{comp}/events/sercs/{serc}/image'
*/
export const addSercImage = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addSercImage.url(args, options),
    method: 'post',
})

addSercImage.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/sercs/{serc}/image',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SERCController::addSercImage
* @see app/Http/Controllers/SERCController.php:271
* @route '/comps/{comp}/events/sercs/{serc}/image'
*/
addSercImage.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return addSercImage.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::addSercImage
* @see app/Http/Controllers/SERCController.php:271
* @route '/comps/{comp}/events/sercs/{serc}/image'
*/
addSercImage.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addSercImage.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SERCController::removeSercImage
* @see app/Http/Controllers/SERCController.php:297
* @route '/comps/{comp}/events/sercs/{serc}/image/remove'
*/
export const removeSercImage = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: removeSercImage.url(args, options),
    method: 'get',
})

removeSercImage.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/image/remove',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::removeSercImage
* @see app/Http/Controllers/SERCController.php:297
* @route '/comps/{comp}/events/sercs/{serc}/image/remove'
*/
removeSercImage.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return removeSercImage.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::removeSercImage
* @see app/Http/Controllers/SERCController.php:297
* @route '/comps/{comp}/events/sercs/{serc}/image/remove'
*/
removeSercImage.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: removeSercImage.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::removeSercImage
* @see app/Http/Controllers/SERCController.php:297
* @route '/comps/{comp}/events/sercs/{serc}/image/remove'
*/
removeSercImage.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: removeSercImage.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERCController::scoringSettings
* @see app/Http/Controllers/SERCController.php:306
* @route '/comps/{comp}/events/sercs/{serc}/scoring-settings'
*/
export const scoringSettings = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scoringSettings.url(args, options),
    method: 'get',
})

scoringSettings.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/scoring-settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::scoringSettings
* @see app/Http/Controllers/SERCController.php:306
* @route '/comps/{comp}/events/sercs/{serc}/scoring-settings'
*/
scoringSettings.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return scoringSettings.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::scoringSettings
* @see app/Http/Controllers/SERCController.php:306
* @route '/comps/{comp}/events/sercs/{serc}/scoring-settings'
*/
scoringSettings.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scoringSettings.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::scoringSettings
* @see app/Http/Controllers/SERCController.php:306
* @route '/comps/{comp}/events/sercs/{serc}/scoring-settings'
*/
scoringSettings.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: scoringSettings.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERCController::saveScoringSettings
* @see app/Http/Controllers/SERCController.php:314
* @route '/comps/{comp}/events/sercs/{serc}/scoring-settings'
*/
export const saveScoringSettings = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveScoringSettings.url(args, options),
    method: 'post',
})

saveScoringSettings.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/sercs/{serc}/scoring-settings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SERCController::saveScoringSettings
* @see app/Http/Controllers/SERCController.php:314
* @route '/comps/{comp}/events/sercs/{serc}/scoring-settings'
*/
saveScoringSettings.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return saveScoringSettings.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::saveScoringSettings
* @see app/Http/Controllers/SERCController.php:314
* @route '/comps/{comp}/events/sercs/{serc}/scoring-settings'
*/
saveScoringSettings.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveScoringSettings.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\SERCController::markSplits
* @see app/Http/Controllers/SERCController.php:377
* @route '/comps/{comp}/events/sercs/{serc}/mark-splits'
*/
export const markSplits = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: markSplits.url(args, options),
    method: 'get',
})

markSplits.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/mark-splits',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::markSplits
* @see app/Http/Controllers/SERCController.php:377
* @route '/comps/{comp}/events/sercs/{serc}/mark-splits'
*/
markSplits.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return markSplits.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::markSplits
* @see app/Http/Controllers/SERCController.php:377
* @route '/comps/{comp}/events/sercs/{serc}/mark-splits'
*/
markSplits.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: markSplits.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::markSplits
* @see app/Http/Controllers/SERCController.php:377
* @route '/comps/{comp}/events/sercs/{serc}/mark-splits'
*/
markSplits.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: markSplits.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERCController::loadMarkSplit
* @see app/Http/Controllers/SERCController.php:382
* @route '/comps/{comp}/events/sercs/{serc}/mark-splits/{judge}'
*/
export const loadMarkSplit = (args: { comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: loadMarkSplit.url(args, options),
    method: 'get',
})

loadMarkSplit.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/mark-splits/{judge}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::loadMarkSplit
* @see app/Http/Controllers/SERCController.php:382
* @route '/comps/{comp}/events/sercs/{serc}/mark-splits/{judge}'
*/
loadMarkSplit.url = (args: { comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            serc: args[1],
            judge: args[2],
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
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return loadMarkSplit.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::loadMarkSplit
* @see app/Http/Controllers/SERCController.php:382
* @route '/comps/{comp}/events/sercs/{serc}/mark-splits/{judge}'
*/
loadMarkSplit.get = (args: { comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: loadMarkSplit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::loadMarkSplit
* @see app/Http/Controllers/SERCController.php:382
* @route '/comps/{comp}/events/sercs/{serc}/mark-splits/{judge}'
*/
loadMarkSplit.head = (args: { comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: loadMarkSplit.url(args, options),
    method: 'head',
})

const SERCController = { add, addPost, view, edit, editPost, printResults, deleteMethod, next, editResultsView, updateTeamResults, hide, addSercImage, removeSercImage, scoringSettings, saveScoringSettings, markSplits, loadMarkSplit, delete: deleteMethod }

export default SERCController