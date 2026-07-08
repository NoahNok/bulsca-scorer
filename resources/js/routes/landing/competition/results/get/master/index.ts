import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Landing\ResultsController::sheet
* @see app/Http/Controllers/Landing/ResultsController.php:110
* @route '/competition/{comp}/results/sheet/master/{schema}'
*/
export const sheet = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sheet.url(args, options),
    method: 'get',
})

sheet.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results/sheet/master/{schema}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\ResultsController::sheet
* @see app/Http/Controllers/Landing/ResultsController.php:110
* @route '/competition/{comp}/results/sheet/master/{schema}'
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
* @see app/Http/Controllers/Landing/ResultsController.php:110
* @route '/competition/{comp}/results/sheet/master/{schema}'
*/
sheet.get = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sheet.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::sheet
* @see app/Http/Controllers/Landing/ResultsController.php:110
* @route '/competition/{comp}/results/sheet/master/{schema}'
*/
sheet.head = (args: { comp: number | { id: number }, schema: number | { id: number } } | [comp: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sheet.url(args, options),
    method: 'head',
})

