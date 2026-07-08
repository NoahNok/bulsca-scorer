import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\SERCController::judge
* @see app/Http/Controllers/SERCController.php:382
* @route '/comps/{comp}/events/sercs/{serc}/mark-splits/{judge}'
*/
export const judge = (args: { comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: judge.url(args, options),
    method: 'get',
})

judge.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/mark-splits/{judge}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::judge
* @see app/Http/Controllers/SERCController.php:382
* @route '/comps/{comp}/events/sercs/{serc}/mark-splits/{judge}'
*/
judge.url = (args: { comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            serc: args[1],
            judge: args[2],
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
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return judge.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::judge
* @see app/Http/Controllers/SERCController.php:382
* @route '/comps/{comp}/events/sercs/{serc}/mark-splits/{judge}'
*/
judge.get = (args: { comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: judge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::judge
* @see app/Http/Controllers/SERCController.php:382
* @route '/comps/{comp}/events/sercs/{serc}/mark-splits/{judge}'
*/
judge.head = (args: { comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: judge.url(args, options),
    method: 'head',
})

const markSplits = {
    judge: Object.assign(judge, judge),
}

export default markSplits