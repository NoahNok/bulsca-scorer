import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::store
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:63
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results'
*/
export const store = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::store
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:63
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results'
*/
store.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return store.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::store
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:63
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results'
*/
store.post = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

