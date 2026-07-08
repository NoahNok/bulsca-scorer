import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Orders\HeatController::generate
* @see app/Http/Controllers/Orders/HeatController.php:26
* @route '/comps/{comp}/heats-and-draws/heats/generate'
*/
export const generate = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generate.url(args, options),
    method: 'get',
})

generate.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/heats-and-draws/heats/generate',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Orders\HeatController::generate
* @see app/Http/Controllers/Orders/HeatController.php:26
* @route '/comps/{comp}/heats-and-draws/heats/generate'
*/
generate.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return generate.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\HeatController::generate
* @see app/Http/Controllers/Orders/HeatController.php:26
* @route '/comps/{comp}/heats-and-draws/heats/generate'
*/
generate.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generate.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Orders\HeatController::generate
* @see app/Http/Controllers/Orders/HeatController.php:26
* @route '/comps/{comp}/heats-and-draws/heats/generate'
*/
generate.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generate.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Orders\HeatController::hide
* @see app/Http/Controllers/Orders/HeatController.php:50
* @route '/comps/{comp}/heats-and-draws/heats/hide'
*/
export const hide = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

hide.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/heats-and-draws/heats/hide',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Orders\HeatController::hide
* @see app/Http/Controllers/Orders/HeatController.php:50
* @route '/comps/{comp}/heats-and-draws/heats/hide'
*/
hide.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return hide.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\HeatController::hide
* @see app/Http/Controllers/Orders/HeatController.php:50
* @route '/comps/{comp}/heats-and-draws/heats/hide'
*/
hide.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Orders\HeatController::hide
* @see app/Http/Controllers/Orders/HeatController.php:50
* @route '/comps/{comp}/heats-and-draws/heats/hide'
*/
hide.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: hide.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Orders\HeatController::edit
* @see app/Http/Controllers/Orders/HeatController.php:58
* @route '/comps/{comp}/heats-and-draws/heats/{event}/edit'
*/
export const edit = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/heats-and-draws/heats/{event}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Orders\HeatController::edit
* @see app/Http/Controllers/Orders/HeatController.php:58
* @route '/comps/{comp}/heats-and-draws/heats/{event}/edit'
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
* @see \App\Http\Controllers\Orders\HeatController::edit
* @see app/Http/Controllers/Orders/HeatController.php:58
* @route '/comps/{comp}/heats-and-draws/heats/{event}/edit'
*/
edit.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Orders\HeatController::edit
* @see app/Http/Controllers/Orders/HeatController.php:58
* @route '/comps/{comp}/heats-and-draws/heats/{event}/edit'
*/
edit.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Orders\HeatController::reset
* @see app/Http/Controllers/Orders/HeatController.php:139
* @route '/comps/{comp}/heats-and-draws/heats/{event}/reset'
*/
export const reset = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reset.url(args, options),
    method: 'get',
})

reset.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/heats-and-draws/heats/{event}/reset',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Orders\HeatController::reset
* @see app/Http/Controllers/Orders/HeatController.php:139
* @route '/comps/{comp}/heats-and-draws/heats/{event}/reset'
*/
reset.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return reset.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\HeatController::reset
* @see app/Http/Controllers/Orders/HeatController.php:139
* @route '/comps/{comp}/heats-and-draws/heats/{event}/reset'
*/
reset.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reset.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Orders\HeatController::reset
* @see app/Http/Controllers/Orders/HeatController.php:139
* @route '/comps/{comp}/heats-and-draws/heats/{event}/reset'
*/
reset.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reset.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Orders\HeatController::swap
* @see app/Http/Controllers/Orders/HeatController.php:63
* @route '/comps/{comp}/heats-and-draws/heats/{event}/swap'
*/
export const swap = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: swap.url(args, options),
    method: 'post',
})

swap.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/heats/{event}/swap',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\HeatController::swap
* @see app/Http/Controllers/Orders/HeatController.php:63
* @route '/comps/{comp}/heats-and-draws/heats/{event}/swap'
*/
swap.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return swap.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\HeatController::swap
* @see app/Http/Controllers/Orders/HeatController.php:63
* @route '/comps/{comp}/heats-and-draws/heats/{event}/swap'
*/
swap.post = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: swap.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Orders\HeatController::swapHeats
* @see app/Http/Controllers/Orders/HeatController.php:107
* @route '/comps/{comp}/heats-and-draws/heats/{event}/swapHeats'
*/
export const swapHeats = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: swapHeats.url(args, options),
    method: 'post',
})

swapHeats.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/heats/{event}/swapHeats',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\HeatController::swapHeats
* @see app/Http/Controllers/Orders/HeatController.php:107
* @route '/comps/{comp}/heats-and-draws/heats/{event}/swapHeats'
*/
swapHeats.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return swapHeats.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\HeatController::swapHeats
* @see app/Http/Controllers/Orders/HeatController.php:107
* @route '/comps/{comp}/heats-and-draws/heats/{event}/swapHeats'
*/
swapHeats.post = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: swapHeats.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Orders\HeatController::deleteHeats
* @see app/Http/Controllers/Orders/HeatController.php:155
* @route '/comps/{comp}/heats-and-draws/heats/{event}/deleteHeat'
*/
export const deleteHeats = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deleteHeats.url(args, options),
    method: 'post',
})

deleteHeats.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/heats/{event}/deleteHeat',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\HeatController::deleteHeats
* @see app/Http/Controllers/Orders/HeatController.php:155
* @route '/comps/{comp}/heats-and-draws/heats/{event}/deleteHeat'
*/
deleteHeats.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return deleteHeats.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\HeatController::deleteHeats
* @see app/Http/Controllers/Orders/HeatController.php:155
* @route '/comps/{comp}/heats-and-draws/heats/{event}/deleteHeat'
*/
deleteHeats.post = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deleteHeats.url(args, options),
    method: 'post',
})

const heats = {
    generate: Object.assign(generate, generate),
    hide: Object.assign(hide, hide),
    edit: Object.assign(edit, edit),
    reset: Object.assign(reset, reset),
    swap: Object.assign(swap, swap),
    swapHeats: Object.assign(swapHeats, swapHeats),
    deleteHeats: Object.assign(deleteHeats, deleteHeats),
}

export default heats