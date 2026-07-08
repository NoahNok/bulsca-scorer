import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::serc
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:201
* @route '//judge.localhost/new/{competition}/serc'
*/
export const serc = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: serc.url(args, options),
    method: 'get',
})

serc.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::serc
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:201
* @route '//judge.localhost/new/{competition}/serc'
*/
serc.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { competition: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { competition: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            competition: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
    }

    return serc.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::serc
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:201
* @route '//judge.localhost/new/{competition}/serc'
*/
serc.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: serc.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::serc
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:201
* @route '//judge.localhost/new/{competition}/serc'
*/
serc.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: serc.url(args, options),
    method: 'head',
})

const competition = {
    serc: Object.assign(serc, serc),
}

export default competition