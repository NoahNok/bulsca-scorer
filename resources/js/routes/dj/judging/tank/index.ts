import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::set
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:84
* @route '//judge.localhost/judging/tank/{tank}'
*/
export const set = (args: { tank: string | number } | [tank: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: set.url(args, options),
    method: 'get',
})

set.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/tank/{tank}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::set
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:84
* @route '//judge.localhost/judging/tank/{tank}'
*/
set.url = (args: { tank: string | number } | [tank: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { tank: args }
    }

    if (Array.isArray(args)) {
        args = {
            tank: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        tank: args.tank,
    }

    return set.definition.url
            .replace('{tank}', parsedArgs.tank.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::set
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:84
* @route '//judge.localhost/judging/tank/{tank}'
*/
set.get = (args: { tank: string | number } | [tank: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: set.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::set
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:84
* @route '//judge.localhost/judging/tank/{tank}'
*/
set.head = (args: { tank: string | number } | [tank: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: set.url(args, options),
    method: 'head',
})

const tank = {
    set: Object.assign(set, set),
}

export default tank