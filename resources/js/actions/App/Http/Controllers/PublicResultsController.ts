import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PublicResultsController::index
* @see app/Http/Controllers/PublicResultsController.php:25
* @route '//results.localhost'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//results.localhost',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::index
* @see app/Http/Controllers/PublicResultsController.php:25
* @route '//results.localhost'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::index
* @see app/Http/Controllers/PublicResultsController.php:25
* @route '//results.localhost'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::index
* @see app/Http/Controllers/PublicResultsController.php:25
* @route '//results.localhost'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
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
* @see \App\Http\Controllers\PublicResultsController::viewComp
* @see app/Http/Controllers/PublicResultsController.php:33
* @route '//results.localhost/{comp}'
*/
export const viewComp = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewComp.url(args, options),
    method: 'get',
})

viewComp.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::viewComp
* @see app/Http/Controllers/PublicResultsController.php:33
* @route '//results.localhost/{comp}'
*/
viewComp.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return viewComp.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::viewComp
* @see app/Http/Controllers/PublicResultsController.php:33
* @route '//results.localhost/{comp}'
*/
viewComp.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewComp.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::viewComp
* @see app/Http/Controllers/PublicResultsController.php:33
* @route '//results.localhost/{comp}'
*/
viewComp.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewComp.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicResultsController::viewSpeed
* @see app/Http/Controllers/PublicResultsController.php:38
* @route '//results.localhost/{comp}/speed/{event}'
*/
export const viewSpeed = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSpeed.url(args, options),
    method: 'get',
})

viewSpeed.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}/speed/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::viewSpeed
* @see app/Http/Controllers/PublicResultsController.php:38
* @route '//results.localhost/{comp}/speed/{event}'
*/
viewSpeed.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return viewSpeed.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::viewSpeed
* @see app/Http/Controllers/PublicResultsController.php:38
* @route '//results.localhost/{comp}/speed/{event}'
*/
viewSpeed.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSpeed.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::viewSpeed
* @see app/Http/Controllers/PublicResultsController.php:38
* @route '//results.localhost/{comp}/speed/{event}'
*/
viewSpeed.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewSpeed.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicResultsController::viewSerc
* @see app/Http/Controllers/PublicResultsController.php:44
* @route '//results.localhost/{comp}/serc/{event}'
*/
export const viewSerc = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSerc.url(args, options),
    method: 'get',
})

viewSerc.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}/serc/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::viewSerc
* @see app/Http/Controllers/PublicResultsController.php:44
* @route '//results.localhost/{comp}/serc/{event}'
*/
viewSerc.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return viewSerc.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::viewSerc
* @see app/Http/Controllers/PublicResultsController.php:44
* @route '//results.localhost/{comp}/serc/{event}'
*/
viewSerc.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewSerc.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::viewSerc
* @see app/Http/Controllers/PublicResultsController.php:44
* @route '//results.localhost/{comp}/serc/{event}'
*/
viewSerc.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewSerc.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicResultsController::viewEntitySercNotes
* @see app/Http/Controllers/PublicResultsController.php:228
* @route '//results.localhost/{comp}/serc/{event}/notes/{entity_id}'
*/
export const viewEntitySercNotes = (args: { comp: number | { id: number }, event: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, event: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewEntitySercNotes.url(args, options),
    method: 'get',
})

viewEntitySercNotes.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}/serc/{event}/notes/{entity_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::viewEntitySercNotes
* @see app/Http/Controllers/PublicResultsController.php:228
* @route '//results.localhost/{comp}/serc/{event}/notes/{entity_id}'
*/
viewEntitySercNotes.url = (args: { comp: number | { id: number }, event: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, event: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            event: args[1],
            entity_id: args[2],
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
        entity_id: args.entity_id,
    }

    return viewEntitySercNotes.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::viewEntitySercNotes
* @see app/Http/Controllers/PublicResultsController.php:228
* @route '//results.localhost/{comp}/serc/{event}/notes/{entity_id}'
*/
viewEntitySercNotes.get = (args: { comp: number | { id: number }, event: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, event: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewEntitySercNotes.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::viewEntitySercNotes
* @see app/Http/Controllers/PublicResultsController.php:228
* @route '//results.localhost/{comp}/serc/{event}/notes/{entity_id}'
*/
viewEntitySercNotes.head = (args: { comp: number | { id: number }, event: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, event: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewEntitySercNotes.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicResultsController::viewResults
* @see app/Http/Controllers/PublicResultsController.php:58
* @route '//results.localhost/{comp}/results/{schema}'
*/
export const viewResults = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewResults.url(args, options),
    method: 'get',
})

viewResults.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}/results/{schema}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::viewResults
* @see app/Http/Controllers/PublicResultsController.php:58
* @route '//results.localhost/{comp}/results/{schema}'
*/
viewResults.url = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return viewResults.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::viewResults
* @see app/Http/Controllers/PublicResultsController.php:58
* @route '//results.localhost/{comp}/results/{schema}'
*/
viewResults.get = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::viewResults
* @see app/Http/Controllers/PublicResultsController.php:58
* @route '//results.localhost/{comp}/results/{schema}'
*/
viewResults.head = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicResultsController::viewDqPen
* @see app/Http/Controllers/PublicResultsController.php:259
* @route '//results.localhost/{comp}/dq-pen/{team}/{code}'
*/
export const viewDqPen = (args: { comp: number | { id: number }, team: number | { id: number }, code: string | number } | [comp: number | { id: number }, team: number | { id: number }, code: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewDqPen.url(args, options),
    method: 'get',
})

viewDqPen.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}/dq-pen/{team}/{code}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::viewDqPen
* @see app/Http/Controllers/PublicResultsController.php:259
* @route '//results.localhost/{comp}/dq-pen/{team}/{code}'
*/
viewDqPen.url = (args: { comp: number | { id: number }, team: number | { id: number }, code: string | number } | [comp: number | { id: number }, team: number | { id: number }, code: string | number ], options?: RouteQueryOptions) => {
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

    return viewDqPen.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{team}', parsedArgs.team.toString())
            .replace('{code}', parsedArgs.code.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::viewDqPen
* @see app/Http/Controllers/PublicResultsController.php:259
* @route '//results.localhost/{comp}/dq-pen/{team}/{code}'
*/
viewDqPen.get = (args: { comp: number | { id: number }, team: number | { id: number }, code: string | number } | [comp: number | { id: number }, team: number | { id: number }, code: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewDqPen.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::viewDqPen
* @see app/Http/Controllers/PublicResultsController.php:259
* @route '//results.localhost/{comp}/dq-pen/{team}/{code}'
*/
viewDqPen.head = (args: { comp: number | { id: number }, team: number | { id: number }, code: string | number } | [comp: number | { id: number }, team: number | { id: number }, code: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewDqPen.url(args, options),
    method: 'head',
})

const PublicResultsController = { index, resolve, viewComp, viewSpeed, viewSerc, viewEntitySercNotes, viewResults, viewDqPen }

export default PublicResultsController