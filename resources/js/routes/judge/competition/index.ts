import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import serc5c2b2c from './serc'
/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::serc
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:77
* @route '//judge.localhost/v2/{competition}/serc/{serc}'
*/
export const serc = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: serc.url(args, options),
    method: 'get',
})

serc.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::serc
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:77
* @route '//judge.localhost/v2/{competition}/serc/{serc}'
*/
serc.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return serc.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::serc
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:77
* @route '//judge.localhost/v2/{competition}/serc/{serc}'
*/
serc.get = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: serc.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::serc
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:77
* @route '//judge.localhost/v2/{competition}/serc/{serc}'
*/
serc.head = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: serc.url(args, options),
    method: 'head',
})

const competition = {
    serc: Object.assign(serc, serc5c2b2c),
}

export default competition