import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::release
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:106
* @route '//judge.localhost/v2/{competition}/sercs/release'
*/
export const release = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: release.url(args, options),
    method: 'post',
})

release.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/sercs/release',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::release
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:106
* @route '//judge.localhost/v2/{competition}/sercs/release'
*/
release.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return release.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::release
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:106
* @route '//judge.localhost/v2/{competition}/sercs/release'
*/
release.post = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: release.url(args, options),
    method: 'post',
})

const sercs = {
    release: Object.assign(release, release),
}

export default sercs