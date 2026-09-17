import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::view
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:94
* @route '//judge.localhost/v2/{competition}/violation/submission/{submission}'
*/
export const view = (args: { competition: number | { id: number }, submission: string | { id: string } } | [competition: number | { id: number }, submission: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/violation/submission/{submission}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::view
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:94
* @route '//judge.localhost/v2/{competition}/violation/submission/{submission}'
*/
view.url = (args: { competition: number | { id: number }, submission: string | { id: string } } | [competition: number | { id: number }, submission: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            submission: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        submission: typeof args.submission === 'object'
        ? args.submission.id
        : args.submission,
    }

    return view.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::view
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:94
* @route '//judge.localhost/v2/{competition}/violation/submission/{submission}'
*/
view.get = (args: { competition: number | { id: number }, submission: string | { id: string } } | [competition: number | { id: number }, submission: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::view
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:94
* @route '//judge.localhost/v2/{competition}/violation/submission/{submission}'
*/
view.head = (args: { competition: number | { id: number }, submission: string | { id: string } } | [competition: number | { id: number }, submission: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationStateController::updateState
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationStateController.php:14
* @route '//judge.localhost/v2/{competition}/violation/submission/{submission}/update-state'
*/
export const updateState = (args: { competition: number | { id: number }, submission: string | { id: string } } | [competition: number | { id: number }, submission: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateState.url(args, options),
    method: 'post',
})

updateState.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/violation/submission/{submission}/update-state',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationStateController::updateState
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationStateController.php:14
* @route '//judge.localhost/v2/{competition}/violation/submission/{submission}/update-state'
*/
updateState.url = (args: { competition: number | { id: number }, submission: string | { id: string } } | [competition: number | { id: number }, submission: string | { id: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            submission: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        submission: typeof args.submission === 'object'
        ? args.submission.id
        : args.submission,
    }

    return updateState.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationStateController::updateState
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationStateController.php:14
* @route '//judge.localhost/v2/{competition}/violation/submission/{submission}/update-state'
*/
updateState.post = (args: { competition: number | { id: number }, submission: string | { id: string } } | [competition: number | { id: number }, submission: string | { id: string } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateState.url(args, options),
    method: 'post',
})

const submission = {
    view: Object.assign(view, view),
    updateState: Object.assign(updateState, updateState),
}

export default submission