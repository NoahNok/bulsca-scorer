import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import serc5c2b2c from './serc'
import stats from './stats'
/**
* @see routes/results.php:12
* @route '//results.localhost/{comp}/unavailable'
*/
export const unavailable = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: unavailable.url(args, options),
    method: 'get',
})

unavailable.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}/unavailable',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/results.php:12
* @route '//results.localhost/{comp}/unavailable'
*/
unavailable.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return unavailable.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/results.php:12
* @route '//results.localhost/{comp}/unavailable'
*/
unavailable.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: unavailable.url(args, options),
    method: 'get',
})

/**
* @see routes/results.php:12
* @route '//results.localhost/{comp}/unavailable'
*/
unavailable.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: unavailable.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicResultsController::resolve
* @see app/Http/Controllers/PublicResultsController.php:249
* @route '//results.localhost/resolve/{date}/{name}'
*/
export const resolve = (args: { date: string | number, name: string | number } | [date: string | number, name: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resolve.url(args, options),
    method: 'get',
})

resolve.definition = {
    methods: ["get","head"],
    url: '//results.localhost/resolve/{date}/{name}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::resolve
* @see app/Http/Controllers/PublicResultsController.php:249
* @route '//results.localhost/resolve/{date}/{name}'
*/
resolve.url = (args: { date: string | number, name: string | number } | [date: string | number, name: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            date: args[0],
            name: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        date: args.date,
        name: args.name,
    }

    return resolve.definition.url
            .replace('{date}', parsedArgs.date.toString())
            .replace('{name}', parsedArgs.name.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::resolve
* @see app/Http/Controllers/PublicResultsController.php:249
* @route '//results.localhost/resolve/{date}/{name}'
*/
resolve.get = (args: { date: string | number, name: string | number } | [date: string | number, name: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resolve.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::resolve
* @see app/Http/Controllers/PublicResultsController.php:249
* @route '//results.localhost/resolve/{date}/{name}'
*/
resolve.head = (args: { date: string | number, name: string | number } | [date: string | number, name: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: resolve.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicResultsController::comp
* @see app/Http/Controllers/PublicResultsController.php:33
* @route '//results.localhost/{comp}'
*/
export const comp = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: comp.url(args, options),
    method: 'get',
})

comp.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::comp
* @see app/Http/Controllers/PublicResultsController.php:33
* @route '//results.localhost/{comp}'
*/
comp.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return comp.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::comp
* @see app/Http/Controllers/PublicResultsController.php:33
* @route '//results.localhost/{comp}'
*/
comp.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: comp.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::comp
* @see app/Http/Controllers/PublicResultsController.php:33
* @route '//results.localhost/{comp}'
*/
comp.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: comp.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicResultsController::speed
* @see app/Http/Controllers/PublicResultsController.php:38
* @route '//results.localhost/{comp}/speed/{event}'
*/
export const speed = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: speed.url(args, options),
    method: 'get',
})

speed.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}/speed/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::speed
* @see app/Http/Controllers/PublicResultsController.php:38
* @route '//results.localhost/{comp}/speed/{event}'
*/
speed.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return speed.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::speed
* @see app/Http/Controllers/PublicResultsController.php:38
* @route '//results.localhost/{comp}/speed/{event}'
*/
speed.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: speed.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::speed
* @see app/Http/Controllers/PublicResultsController.php:38
* @route '//results.localhost/{comp}/speed/{event}'
*/
speed.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: speed.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicResultsController::serc
* @see app/Http/Controllers/PublicResultsController.php:44
* @route '//results.localhost/{comp}/serc/{event}'
*/
export const serc = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: serc.url(args, options),
    method: 'get',
})

serc.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}/serc/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::serc
* @see app/Http/Controllers/PublicResultsController.php:44
* @route '//results.localhost/{comp}/serc/{event}'
*/
serc.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return serc.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::serc
* @see app/Http/Controllers/PublicResultsController.php:44
* @route '//results.localhost/{comp}/serc/{event}'
*/
serc.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: serc.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::serc
* @see app/Http/Controllers/PublicResultsController.php:44
* @route '//results.localhost/{comp}/serc/{event}'
*/
serc.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: serc.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicResultsController::results
* @see app/Http/Controllers/PublicResultsController.php:58
* @route '//results.localhost/{comp}/results/{schema}'
*/
export const results = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

results.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}/results/{schema}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::results
* @see app/Http/Controllers/PublicResultsController.php:58
* @route '//results.localhost/{comp}/results/{schema}'
*/
results.url = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return results.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::results
* @see app/Http/Controllers/PublicResultsController.php:58
* @route '//results.localhost/{comp}/results/{schema}'
*/
results.get = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::results
* @see app/Http/Controllers/PublicResultsController.php:58
* @route '//results.localhost/{comp}/results/{schema}'
*/
results.head = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: results.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicResultsController::dqPen
* @see app/Http/Controllers/PublicResultsController.php:259
* @route '//results.localhost/{comp}/dq-pen/{team}/{code}'
*/
export const dqPen = (args: { comp: number | { id: number }, team: number | { id: number }, code: string | number } | [comp: number | { id: number }, team: number | { id: number }, code: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dqPen.url(args, options),
    method: 'get',
})

dqPen.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}/dq-pen/{team}/{code}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::dqPen
* @see app/Http/Controllers/PublicResultsController.php:259
* @route '//results.localhost/{comp}/dq-pen/{team}/{code}'
*/
dqPen.url = (args: { comp: number | { id: number }, team: number | { id: number }, code: string | number } | [comp: number | { id: number }, team: number | { id: number }, code: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            team: args[1],
            code: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        team: typeof args.team === 'object'
        ? args.team.id
        : args.team,
        code: args.code,
    }

    return dqPen.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{team}', parsedArgs.team.toString())
            .replace('{code}', parsedArgs.code.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::dqPen
* @see app/Http/Controllers/PublicResultsController.php:259
* @route '//results.localhost/{comp}/dq-pen/{team}/{code}'
*/
dqPen.get = (args: { comp: number | { id: number }, team: number | { id: number }, code: string | number } | [comp: number | { id: number }, team: number | { id: number }, code: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dqPen.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::dqPen
* @see app/Http/Controllers/PublicResultsController.php:259
* @route '//results.localhost/{comp}/dq-pen/{team}/{code}'
*/
dqPen.head = (args: { comp: number | { id: number }, team: number | { id: number }, code: string | number } | [comp: number | { id: number }, team: number | { id: number }, code: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dqPen.url(args, options),
    method: 'head',
})

const resultsNamespace = {
    unavailable: Object.assign(unavailable, unavailable),
    resolve: Object.assign(resolve, resolve),
    comp: Object.assign(comp, comp),
    speed: Object.assign(speed, speed),
    serc: Object.assign(serc, serc5c2b2c),
    results: Object.assign(results, results),
    dqPen: Object.assign(dqPen, dqPen),
    stats: Object.assign(stats, stats),
}

export default resultsNamespace