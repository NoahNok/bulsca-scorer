import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::store
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:200
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}'
*/
export const store = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::store
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:200
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}'
*/
store.url = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            event: args[1],
            heat: args[2],
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
        heat: args.heat,
    }

    return store.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace('{heat}', parsedArgs.heat.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::store
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:200
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}'
*/
store.post = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

const mark = {
    store: Object.assign(store, store),
}

export default mark