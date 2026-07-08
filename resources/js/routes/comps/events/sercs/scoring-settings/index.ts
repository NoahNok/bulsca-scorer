import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\SERCController::save
* @see app/Http/Controllers/SERCController.php:314
* @route '/comps/{comp}/events/sercs/{serc}/scoring-settings'
*/
export const save = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: save.url(args, options),
    method: 'post',
})

save.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/sercs/{serc}/scoring-settings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SERCController::save
* @see app/Http/Controllers/SERCController.php:314
* @route '/comps/{comp}/events/sercs/{serc}/scoring-settings'
*/
save.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return save.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::save
* @see app/Http/Controllers/SERCController.php:314
* @route '/comps/{comp}/events/sercs/{serc}/scoring-settings'
*/
save.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: save.url(args, options),
    method: 'post',
})

const scoringSettings = {
    save: Object.assign(save, save),
}

export default scoringSettings