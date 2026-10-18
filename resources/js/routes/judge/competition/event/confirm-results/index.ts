import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::store
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:83
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
export const store = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/event/{event}/confirm-results',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::store
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:83
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
store.url = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            event: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return store.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::store
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:83
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
store.post = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

const confirmResults = {
    store: Object.assign(store, store),
}

export default confirmResults