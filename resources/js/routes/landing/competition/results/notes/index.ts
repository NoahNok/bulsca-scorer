import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Landing\ResultsController::serc
* @see app/Http/Controllers/Landing/ResultsController.php:183
* @route '/competition/{comp}/results/notes/serc/{serc}/{entity_id}'
*/
export const serc = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: serc.url(args, options),
    method: 'get',
})

serc.definition = {
    methods: ["get","head"],
    url: '/competition/{comp}/results/notes/serc/{serc}/{entity_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\ResultsController::serc
* @see app/Http/Controllers/Landing/ResultsController.php:183
* @route '/competition/{comp}/results/notes/serc/{serc}/{entity_id}'
*/
serc.url = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
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

    return serc.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\ResultsController::serc
* @see app/Http/Controllers/Landing/ResultsController.php:183
* @route '/competition/{comp}/results/notes/serc/{serc}/{entity_id}'
*/
serc.get = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: serc.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\ResultsController::serc
* @see app/Http/Controllers/Landing/ResultsController.php:183
* @route '/competition/{comp}/results/notes/serc/{serc}/{entity_id}'
*/
serc.head = (args: { comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: serc.url(args, options),
    method: 'head',
})

const notes = {
    serc: Object.assign(serc, serc),
}

export default notes