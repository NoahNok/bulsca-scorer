import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Landing\ResultsController::sheet
* @see app/Http/Controllers/Landing/ResultsController.php:77
* @route '/competition/{comp}/results/sheet/{schema}'
*/
export const sheet = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sheet.url(args, options),
    method: 'get',
})

sheet.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results/sheet/{schema}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\ResultsController::sheet
* @see app/Http/Controllers/Landing/ResultsController.php:77
* @route '/competition/{comp}/results/sheet/{schema}'
*/
sheet.url = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return sheet.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\ResultsController::sheet
* @see app/Http/Controllers/Landing/ResultsController.php:77
* @route '/competition/{comp}/results/sheet/{schema}'
*/
sheet.get = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sheet.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::sheet
* @see app/Http/Controllers/Landing/ResultsController.php:77
* @route '/competition/{comp}/results/sheet/{schema}'
*/
sheet.head = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sheet.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::violation
* @see app/Http/Controllers/Landing/ResultsController.php:133
* @route '/competition/{comp}/results/violation/{violation_id}/{violation_type}'
*/
export const violation = (args: { comp: number | { id: number }, violation_id: string | number, violation_type: string | number } | [comp: number | { id: number }, violation_id: string | number, violation_type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: violation.url(args, options),
    method: 'get',
})

violation.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results/violation/{violation_id}/{violation_type}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\ResultsController::violation
* @see app/Http/Controllers/Landing/ResultsController.php:133
* @route '/competition/{comp}/results/violation/{violation_id}/{violation_type}'
*/
violation.url = (args: { comp: number | { id: number }, violation_id: string | number, violation_type: string | number } | [comp: number | { id: number }, violation_id: string | number, violation_type: string | number ], options?: RouteQueryOptions) => {
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

    return violation.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{violation_id}', parsedArgs.violation_id.toString())
            .replace('{violation_type}', parsedArgs.violation_type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\ResultsController::violation
* @see app/Http/Controllers/Landing/ResultsController.php:133
* @route '/competition/{comp}/results/violation/{violation_id}/{violation_type}'
*/
violation.get = (args: { comp: number | { id: number }, violation_id: string | number, violation_type: string | number } | [comp: number | { id: number }, violation_id: string | number, violation_type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: violation.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::violation
* @see app/Http/Controllers/Landing/ResultsController.php:133
* @route '/competition/{comp}/results/violation/{violation_id}/{violation_type}'
*/
violation.head = (args: { comp: number | { id: number }, violation_id: string | number, violation_type: string | number } | [comp: number | { id: number }, violation_id: string | number, violation_type: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: violation.url(args, options),
    method: 'head',
})

