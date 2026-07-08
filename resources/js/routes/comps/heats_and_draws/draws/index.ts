import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
import tank_setupA39b72 from './tank_setup'
/**
* @see \App\Http\Controllers\Orders\DrawController::generate
* @see app/Http/Controllers/Orders/DrawController.php:19
* @route '/comps/{comp}/heats-and-draws/draws/generate'
*/
export const generate = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generate.url(args, options),
    method: 'get',
})

generate.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/heats-and-draws/draws/generate',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::generate
* @see app/Http/Controllers/Orders/DrawController.php:19
* @route '/comps/{comp}/heats-and-draws/draws/generate'
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
* @see \App\Http\Controllers\Orders\DrawController::generate
* @see app/Http/Controllers/Orders/DrawController.php:19
* @route '/comps/{comp}/heats-and-draws/draws/generate'
*/
generate.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: generate.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::generate
* @see app/Http/Controllers/Orders/DrawController.php:19
* @route '/comps/{comp}/heats-and-draws/draws/generate'
*/
generate.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: generate.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::hide
* @see app/Http/Controllers/Orders/DrawController.php:37
* @route '/comps/{comp}/heats-and-draws/draws/hide'
*/
export const hide = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

hide.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/heats-and-draws/draws/hide',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::hide
* @see app/Http/Controllers/Orders/DrawController.php:37
* @route '/comps/{comp}/heats-and-draws/draws/hide'
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
* @see \App\Http\Controllers\Orders\DrawController::hide
* @see app/Http/Controllers/Orders/DrawController.php:37
* @route '/comps/{comp}/heats-and-draws/draws/hide'
*/
hide.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::hide
* @see app/Http/Controllers/Orders/DrawController.php:37
* @route '/comps/{comp}/heats-and-draws/draws/hide'
*/
hide.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: hide.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::tank_setup
* @see app/Http/Controllers/Orders/DrawController.php:90
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
export const tank_setup = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tank_setup.url(args, options),
    method: 'get',
})

tank_setup.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/heats-and-draws/draws/tank-setup',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::tank_setup
* @see app/Http/Controllers/Orders/DrawController.php:90
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
tank_setup.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return tank_setup.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\DrawController::tank_setup
* @see app/Http/Controllers/Orders/DrawController.php:90
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
tank_setup.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tank_setup.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::tank_setup
* @see app/Http/Controllers/Orders/DrawController.php:90
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
tank_setup.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: tank_setup.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::edit
* @see app/Http/Controllers/Orders/DrawController.php:46
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/edit'
*/
export const edit = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/heats-and-draws/draws/{serc}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::edit
* @see app/Http/Controllers/Orders/DrawController.php:46
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/edit'
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
* @see \App\Http\Controllers\Orders\DrawController::edit
* @see app/Http/Controllers/Orders/DrawController.php:46
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/edit'
*/
edit.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::edit
* @see app/Http/Controllers/Orders/DrawController.php:46
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/edit'
*/
edit.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::swap
* @see app/Http/Controllers/Orders/DrawController.php:52
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/edit'
*/
export const swap = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: swap.url(args, options),
    method: 'post',
})

swap.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/draws/{serc}/edit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::swap
* @see app/Http/Controllers/Orders/DrawController.php:52
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/edit'
*/
swap.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return swap.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\DrawController::swap
* @see app/Http/Controllers/Orders/DrawController.php:52
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/edit'
*/
swap.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: swap.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::reset
* @see app/Http/Controllers/Orders/DrawController.php:79
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/reset'
*/
export const reset = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reset.url(args, options),
    method: 'get',
})

reset.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/heats-and-draws/draws/{serc}/reset',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::reset
* @see app/Http/Controllers/Orders/DrawController.php:79
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/reset'
*/
reset.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return reset.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\DrawController::reset
* @see app/Http/Controllers/Orders/DrawController.php:79
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/reset'
*/
reset.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reset.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::reset
* @see app/Http/Controllers/Orders/DrawController.php:79
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/reset'
*/
reset.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reset.url(args, options),
    method: 'head',
})

const draws = {
    generate: Object.assign(generate, generate),
    hide: Object.assign(hide, hide),
    tank_setup: Object.assign(tank_setup, tank_setupA39b72),
    edit: Object.assign(edit, edit),
    swap: Object.assign(swap, swap),
    reset: Object.assign(reset, reset),
}

export default draws