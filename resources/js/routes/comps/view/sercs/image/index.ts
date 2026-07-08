import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\SERCController::remove
* @see app/Http/Controllers/SERCController.php:297
* @route '/comps/{comp}/events/sercs/{serc}/image/remove'
*/
export const remove = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: remove.url(args, options),
    method: 'get',
})

remove.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/image/remove',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::remove
* @see app/Http/Controllers/SERCController.php:297
* @route '/comps/{comp}/events/sercs/{serc}/image/remove'
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
* @see \App\Http\Controllers\SERCController::remove
* @see app/Http/Controllers/SERCController.php:297
* @route '/comps/{comp}/events/sercs/{serc}/image/remove'
*/
remove.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: remove.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::remove
* @see app/Http/Controllers/SERCController.php:297
* @route '/comps/{comp}/events/sercs/{serc}/image/remove'
*/
remove.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: remove.url(args, options),
    method: 'head',
})

const image = {
    remove: Object.assign(remove, remove),
}

export default image