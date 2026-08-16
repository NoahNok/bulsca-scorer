import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::post
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:45
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm'
*/
export const post = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/confirm',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::post
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:45
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm'
*/
post.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return post.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::post
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:45
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm'
*/
post.post = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

