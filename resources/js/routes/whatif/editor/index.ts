import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::speeds
* @see app/Http/Controllers/WhatIf/WhatIfController.php:249
* @route '//whatif.localhost/editor/results/speeds/{speed}'
*/
export const speeds = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: speeds.url(args, options),
    method: 'get',
})

speeds.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor/results/speeds/{speed}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::speeds
* @see app/Http/Controllers/WhatIf/WhatIfController.php:249
* @route '//whatif.localhost/editor/results/speeds/{speed}'
*/
speeds.url = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { speed: args }
    }

    if (Array.isArray(args)) {
        args = {
            speed: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        speed: args.speed,
    }

    return speeds.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::speeds
* @see app/Http/Controllers/WhatIf/WhatIfController.php:249
* @route '//whatif.localhost/editor/results/speeds/{speed}'
*/
speeds.get = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: speeds.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::speeds
* @see app/Http/Controllers/WhatIf/WhatIfController.php:249
* @route '//whatif.localhost/editor/results/speeds/{speed}'
*/
speeds.head = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: speeds.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::sercs
* @see app/Http/Controllers/WhatIf/WhatIfController.php:266
* @route '//whatif.localhost/editor/results/sercs/{serc}'
*/
export const sercs = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sercs.url(args, options),
    method: 'get',
})

sercs.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor/results/sercs/{serc}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::sercs
* @see app/Http/Controllers/WhatIf/WhatIfController.php:266
* @route '//whatif.localhost/editor/results/sercs/{serc}'
*/
sercs.url = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { serc: args }
    }

    if (Array.isArray(args)) {
        args = {
            serc: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        serc: args.serc,
    }

    return sercs.definition.url
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::sercs
* @see app/Http/Controllers/WhatIf/WhatIfController.php:266
* @route '//whatif.localhost/editor/results/sercs/{serc}'
*/
sercs.get = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sercs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::sercs
* @see app/Http/Controllers/WhatIf/WhatIfController.php:266
* @route '//whatif.localhost/editor/results/sercs/{serc}'
*/
sercs.head = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sercs.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::results
* @see app/Http/Controllers/WhatIf/WhatIfController.php:126
* @route '//whatif.localhost/editor/results/{schema}'
*/
export const results = (args: { schema: string | number } | [schema: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

results.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor/results/{schema}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::results
* @see app/Http/Controllers/WhatIf/WhatIfController.php:126
* @route '//whatif.localhost/editor/results/{schema}'
*/
results.url = (args: { schema: string | number } | [schema: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schema: args }
    }

    if (Array.isArray(args)) {
        args = {
            schema: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        schema: args.schema,
    }

    return results.definition.url
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::results
* @see app/Http/Controllers/WhatIf/WhatIfController.php:126
* @route '//whatif.localhost/editor/results/{schema}'
*/
results.get = (args: { schema: string | number } | [schema: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::results
* @see app/Http/Controllers/WhatIf/WhatIfController.php:126
* @route '//whatif.localhost/editor/results/{schema}'
*/
results.head = (args: { schema: string | number } | [schema: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: results.url(args, options),
    method: 'head',
})

const editor = {
    speeds: Object.assign(speeds, speeds),
    sercs: Object.assign(sercs, sercs),
    results: Object.assign(results, results),
}

export default editor