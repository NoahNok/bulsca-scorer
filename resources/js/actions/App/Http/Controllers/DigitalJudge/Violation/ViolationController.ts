import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::submissions
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:12
* @route '//judge.localhost/v2/{competition}/violation/submissions'
*/
export const submissions = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: submissions.url(args, options),
    method: 'get',
})

submissions.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/violation/submissions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::submissions
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:12
* @route '//judge.localhost/v2/{competition}/violation/submissions'
*/
submissions.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return submissions.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::submissions
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:12
* @route '//judge.localhost/v2/{competition}/violation/submissions'
*/
submissions.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: submissions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::submissions
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:12
* @route '//judge.localhost/v2/{competition}/violation/submissions'
*/
submissions.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: submissions.url(args, options),
    method: 'head',
})

const ViolationController = { submissions }

export default ViolationController