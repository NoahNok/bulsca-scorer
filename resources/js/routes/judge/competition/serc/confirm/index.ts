import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::post
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:171
* @route '//judge.localhost/new/{competition}/serc/confirm'
*/
export const post = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '//judge.localhost/new/{competition}/serc/confirm',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::post
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:171
* @route '//judge.localhost/new/{competition}/serc/confirm'
*/
post.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return post.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::post
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:171
* @route '//judge.localhost/new/{competition}/serc/confirm'
*/
post.post = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

