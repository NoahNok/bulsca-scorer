import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
import scoringSettings68487f from './scoring-settings'
import markSplits151b30 from './mark-splits'
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
* @see \App\Http\Controllers\SERCController::editResults
* @see app/Http/Controllers/SERCController.php:132
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
export const editResults = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editResults.url(args, options),
    method: 'get',
})

editResults.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::editResults
* @see app/Http/Controllers/SERCController.php:132
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
editResults.url = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
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

    return editResults.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::editResults
* @see app/Http/Controllers/SERCController.php:132
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
editResults.get = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::editResults
* @see app/Http/Controllers/SERCController.php:132
* @route '/comps/{comp}/events/sercs/{serc}/results/{entity_id}/edit'
*/
editResults.head = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: editResults.url(args, options),
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

const sercs = {
    add: Object.assign(add, add),
    addPost: Object.assign(addPost, addPost),
    view: Object.assign(view, view),
    edit: Object.assign(edit, edit),
    editPost: Object.assign(editPost, editPost),
    editResults: Object.assign(editResults, editResults),
    scoringSettings: Object.assign(scoringSettings, scoringSettings68487f),
    markSplits: Object.assign(markSplits, markSplits151b30),
}

export default sercs