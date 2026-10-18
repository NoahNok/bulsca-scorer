import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Landing\ResultsController::showSercBreakdown
* @see app/Http/Controllers/Landing/ResultsController.php:173
* @route '/competition/{comp}/results/breakdown/serc/{serc}'
*/
export const showSercBreakdown = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showSercBreakdown.url(args, options),
    method: 'get',
})

showSercBreakdown.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results/breakdown/serc/{serc}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\ResultsController::showSercBreakdown
* @see app/Http/Controllers/Landing/ResultsController.php:173
* @route '/competition/{comp}/results/breakdown/serc/{serc}'
*/
showSercBreakdown.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return showSercBreakdown.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\ResultsController::showSercBreakdown
* @see app/Http/Controllers/Landing/ResultsController.php:173
* @route '/competition/{comp}/results/breakdown/serc/{serc}'
*/
showSercBreakdown.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showSercBreakdown.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::showSercBreakdown
* @see app/Http/Controllers/Landing/ResultsController.php:173
* @route '/competition/{comp}/results/breakdown/serc/{serc}'
*/
showSercBreakdown.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showSercBreakdown.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::getSercNote
* @see app/Http/Controllers/Landing/ResultsController.php:183
* @route '/competition/{comp}/results/notes/serc/{serc}/{entity_id}'
*/
export const getSercNote = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSercNote.url(args, options),
    method: 'get',
})

getSercNote.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results/notes/serc/{serc}/{entity_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\ResultsController::getSercNote
* @see app/Http/Controllers/Landing/ResultsController.php:183
* @route '/competition/{comp}/results/notes/serc/{serc}/{entity_id}'
*/
getSercNote.url = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
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

    return getSercNote.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\ResultsController::getSercNote
* @see app/Http/Controllers/Landing/ResultsController.php:183
* @route '/competition/{comp}/results/notes/serc/{serc}/{entity_id}'
*/
getSercNote.get = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSercNote.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::getSercNote
* @see app/Http/Controllers/Landing/ResultsController.php:183
* @route '/competition/{comp}/results/notes/serc/{serc}/{entity_id}'
*/
getSercNote.head = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSercNote.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::getMasterSheetResults
* @see app/Http/Controllers/Landing/ResultsController.php:110
* @route '/competition/{comp}/results/sheet/master/{schema}'
*/
export const getMasterSheetResults = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMasterSheetResults.url(args, options),
    method: 'get',
})

getMasterSheetResults.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results/sheet/master/{schema}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\ResultsController::getMasterSheetResults
* @see app/Http/Controllers/Landing/ResultsController.php:110
* @route '/competition/{comp}/results/sheet/master/{schema}'
*/
getMasterSheetResults.url = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return getMasterSheetResults.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\ResultsController::getMasterSheetResults
* @see app/Http/Controllers/Landing/ResultsController.php:110
* @route '/competition/{comp}/results/sheet/master/{schema}'
*/
getMasterSheetResults.get = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getMasterSheetResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::getMasterSheetResults
* @see app/Http/Controllers/Landing/ResultsController.php:110
* @route '/competition/{comp}/results/sheet/master/{schema}'
*/
getMasterSheetResults.head = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getMasterSheetResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::getSheetResults
* @see app/Http/Controllers/Landing/ResultsController.php:77
* @route '/competition/{comp}/results/sheet/{schema}'
*/
export const getSheetResults = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSheetResults.url(args, options),
    method: 'get',
})

getSheetResults.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results/sheet/{schema}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\ResultsController::getSheetResults
* @see app/Http/Controllers/Landing/ResultsController.php:77
* @route '/competition/{comp}/results/sheet/{schema}'
*/
getSheetResults.url = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return getSheetResults.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\ResultsController::getSheetResults
* @see app/Http/Controllers/Landing/ResultsController.php:77
* @route '/competition/{comp}/results/sheet/{schema}'
*/
getSheetResults.get = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSheetResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::getSheetResults
* @see app/Http/Controllers/Landing/ResultsController.php:77
* @route '/competition/{comp}/results/sheet/{schema}'
*/
getSheetResults.head = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSheetResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::getViolation
* @see app/Http/Controllers/Landing/ResultsController.php:133
* @route '/competition/{comp}/results/violation/{violation_id}/{violation_type}'
*/
export const getViolation = (args: { comp: number | { id: number }, violation_id: string | number, violation_type: string | number } | [comp: number | { id: number }, violation_id: string | number, violation_type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getViolation.url(args, options),
    method: 'get',
})

getViolation.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results/violation/{violation_id}/{violation_type}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\ResultsController::getViolation
* @see app/Http/Controllers/Landing/ResultsController.php:133
* @route '/competition/{comp}/results/violation/{violation_id}/{violation_type}'
*/
getViolation.url = (args: { comp: number | { id: number }, violation_id: string | number, violation_type: string | number } | [comp: number | { id: number }, violation_id: string | number, violation_type: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            violation_id: args[1],
            violation_type: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        violation_id: args.violation_id,
        violation_type: args.violation_type,
    }

    return getViolation.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{violation_id}', parsedArgs.violation_id.toString())
            .replace('{violation_type}', parsedArgs.violation_type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\ResultsController::getViolation
* @see app/Http/Controllers/Landing/ResultsController.php:133
* @route '/competition/{comp}/results/violation/{violation_id}/{violation_type}'
*/
getViolation.get = (args: { comp: number | { id: number }, violation_id: string | number, violation_type: string | number } | [comp: number | { id: number }, violation_id: string | number, violation_type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getViolation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::getViolation
* @see app/Http/Controllers/Landing/ResultsController.php:133
* @route '/competition/{comp}/results/violation/{violation_id}/{violation_type}'
*/
getViolation.head = (args: { comp: number | { id: number }, violation_id: string | number, violation_type: string | number } | [comp: number | { id: number }, violation_id: string | number, violation_type: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getViolation.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::getEventResults
* @see app/Http/Controllers/Landing/ResultsController.php:25
* @route '/competition/{comp}/results/{league}/{event}-{type}'
*/
export const getEventResults = (args: { comp: number | { id: number }, league: string | number, event: string | number, type: string | number } | [comp: number | { id: number }, league: string | number, event: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEventResults.url(args, options),
    method: 'get',
})

getEventResults.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results/{league}/{event}-{type}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\ResultsController::getEventResults
* @see app/Http/Controllers/Landing/ResultsController.php:25
* @route '/competition/{comp}/results/{league}/{event}-{type}'
*/
getEventResults.url = (args: { comp: number | { id: number }, league: string | number, event: string | number, type: string | number } | [comp: number | { id: number }, league: string | number, event: string | number, type: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            league: args[1],
            event: args[2],
            type: args[3],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        league: args.league,
        event: args.event,
        type: args.type,
    }

    return getEventResults.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{league}', parsedArgs.league.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace('{type}', parsedArgs.type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\ResultsController::getEventResults
* @see app/Http/Controllers/Landing/ResultsController.php:25
* @route '/competition/{comp}/results/{league}/{event}-{type}'
*/
getEventResults.get = (args: { comp: number | { id: number }, league: string | number, event: string | number, type: string | number } | [comp: number | { id: number }, league: string | number, event: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEventResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::getEventResults
* @see app/Http/Controllers/Landing/ResultsController.php:25
* @route '/competition/{comp}/results/{league}/{event}-{type}'
*/
getEventResults.head = (args: { comp: number | { id: number }, league: string | number, event: string | number, type: string | number } | [comp: number | { id: number }, league: string | number, event: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getEventResults.url(args, options),
    method: 'head',
})

const ResultsController = { showSercBreakdown, getSercNote, getMasterSheetResults, getSheetResults, getViolation, getEventResults }

export default ResultsController