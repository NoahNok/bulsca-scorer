import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
import scoringSettings68487f from './scoring-settings'
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

const speeds = {
    add: Object.assign(add, add),
    view: Object.assign(view, view),
    editResult: Object.assign(editResult, editResult),
    edit: Object.assign(edit, edit),
    editPost: Object.assign(editPost, editPost),
    scoringSettings: Object.assign(scoringSettings, scoringSettings68487f),
}

export default speeds