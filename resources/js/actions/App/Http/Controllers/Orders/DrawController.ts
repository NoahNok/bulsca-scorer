import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Orders\DrawController::tankSetup
* @see app/Http/Controllers/Orders/DrawController.php:320
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
export const tankSetup = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tankSetup.url(args, options),
    method: 'get',
})

tankSetup.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/heats-and-draws/draws/tank-setup',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::tankSetup
* @see app/Http/Controllers/Orders/DrawController.php:320
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
tankSetup.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return tankSetup.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\DrawController::tankSetup
* @see app/Http/Controllers/Orders/DrawController.php:320
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
tankSetup.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tankSetup.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::tankSetup
* @see app/Http/Controllers/Orders/DrawController.php:320
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
tankSetup.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: tankSetup.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::tankSetupPost
* @see app/Http/Controllers/Orders/DrawController.php:325
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
export const tankSetupPost = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: tankSetupPost.url(args, options),
    method: 'post',
})

tankSetupPost.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/draws/tank-setup',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::tankSetupPost
* @see app/Http/Controllers/Orders/DrawController.php:325
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
tankSetupPost.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return tankSetupPost.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\DrawController::tankSetupPost
* @see app/Http/Controllers/Orders/DrawController.php:325
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
tankSetupPost.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: tankSetupPost.url(args, options),
    method: 'post',
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
* @see app/Http/Controllers/Orders/DrawController.php:53
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
* @see app/Http/Controllers/Orders/DrawController.php:53
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
* @see app/Http/Controllers/Orders/DrawController.php:53
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/edit'
*/
swap.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: swap.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::remove
* @see app/Http/Controllers/Orders/DrawController.php:75
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/remove'
*/
export const remove = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: remove.url(args, options),
    method: 'post',
})

remove.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/draws/{serc}/remove',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::remove
* @see app/Http/Controllers/Orders/DrawController.php:75
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/remove'
*/
remove.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return remove.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\DrawController::remove
* @see app/Http/Controllers/Orders/DrawController.php:75
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/remove'
*/
remove.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: remove.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::move
* @see app/Http/Controllers/Orders/DrawController.php:98
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/move'
*/
export const move = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: move.url(args, options),
    method: 'post',
})

move.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/draws/{serc}/move',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::move
* @see app/Http/Controllers/Orders/DrawController.php:98
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/move'
*/
move.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return move.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\DrawController::move
* @see app/Http/Controllers/Orders/DrawController.php:98
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/move'
*/
move.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: move.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::assign
* @see app/Http/Controllers/Orders/DrawController.php:137
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/assign'
*/
export const assign = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: assign.url(args, options),
    method: 'post',
})

assign.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/draws/{serc}/assign',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::assign
* @see app/Http/Controllers/Orders/DrawController.php:137
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/assign'
*/
assign.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return assign.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\DrawController::assign
* @see app/Http/Controllers/Orders/DrawController.php:137
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/assign'
*/
assign.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: assign.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::addTank
* @see app/Http/Controllers/Orders/DrawController.php:171
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/add-tank'
*/
export const addTank = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addTank.url(args, options),
    method: 'post',
})

addTank.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/draws/{serc}/add-tank',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::addTank
* @see app/Http/Controllers/Orders/DrawController.php:171
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/add-tank'
*/
addTank.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return addTank.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\DrawController::addTank
* @see app/Http/Controllers/Orders/DrawController.php:171
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/add-tank'
*/
addTank.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addTank.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::compactTanks
* @see app/Http/Controllers/Orders/DrawController.php:188
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/compact-tanks'
*/
export const compactTanks = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compactTanks.url(args, options),
    method: 'post',
})

compactTanks.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/draws/{serc}/compact-tanks',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::compactTanks
* @see app/Http/Controllers/Orders/DrawController.php:188
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/compact-tanks'
*/
compactTanks.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return compactTanks.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\DrawController::compactTanks
* @see app/Http/Controllers/Orders/DrawController.php:188
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/compact-tanks'
*/
compactTanks.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: compactTanks.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::removeTank
* @see app/Http/Controllers/Orders/DrawController.php:253
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/remove-tank'
*/
export const removeTank = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeTank.url(args, options),
    method: 'post',
})

removeTank.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/draws/{serc}/remove-tank',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::removeTank
* @see app/Http/Controllers/Orders/DrawController.php:253
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/remove-tank'
*/
removeTank.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return removeTank.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\DrawController::removeTank
* @see app/Http/Controllers/Orders/DrawController.php:253
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/remove-tank'
*/
removeTank.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeTank.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Orders\DrawController::reset
* @see app/Http/Controllers/Orders/DrawController.php:303
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/reset'
*/
export const reset = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reset.url(args, options),
    method: 'post',
})

reset.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/draws/{serc}/reset',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::reset
* @see app/Http/Controllers/Orders/DrawController.php:303
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
* @see app/Http/Controllers/Orders/DrawController.php:303
* @route '/comps/{comp}/heats-and-draws/draws/{serc}/reset'
*/
reset.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reset.url(args, options),
    method: 'post',
})

const DrawController = { generate, hide, tankSetup, tankSetupPost, edit, swap, remove, move, assign, addTank, compactTanks, removeTank, reset }

export default DrawController