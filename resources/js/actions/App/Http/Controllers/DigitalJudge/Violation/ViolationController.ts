import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::submissions
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:23
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
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:23
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
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:23
* @route '//judge.localhost/v2/{competition}/violation/submissions'
*/
submissions.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: submissions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::submissions
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:23
* @route '//judge.localhost/v2/{competition}/violation/submissions'
*/
submissions.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: submissions.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::issue
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:37
* @route '//judge.localhost/v2/{competition}/violation/issue'
*/
export const issue = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: issue.url(args, options),
    method: 'get',
})

issue.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/violation/issue',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::issue
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:37
* @route '//judge.localhost/v2/{competition}/violation/issue'
*/
issue.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return issue.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::issue
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:37
* @route '//judge.localhost/v2/{competition}/violation/issue'
*/
issue.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: issue.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::issue
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:37
* @route '//judge.localhost/v2/{competition}/violation/issue'
*/
issue.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: issue.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::submit
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:50
* @route '//judge.localhost/v2/{competition}/violation/issue'
*/
export const submit = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/violation/issue',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::submit
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:50
* @route '//judge.localhost/v2/{competition}/violation/issue'
*/
submit.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return submit.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::submit
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:50
* @route '//judge.localhost/v2/{competition}/violation/issue'
*/
submit.post = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getHeatsFor
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:100
* @route '//judge.localhost/v2/{competition}/violation/heats/{event}'
*/
export const getHeatsFor = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getHeatsFor.url(args, options),
    method: 'get',
})

getHeatsFor.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/violation/heats/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getHeatsFor
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:100
* @route '//judge.localhost/v2/{competition}/violation/heats/{event}'
*/
getHeatsFor.url = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return getHeatsFor.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getHeatsFor
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:100
* @route '//judge.localhost/v2/{competition}/violation/heats/{event}'
*/
getHeatsFor.get = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getHeatsFor.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getHeatsFor
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:100
* @route '//judge.localhost/v2/{competition}/violation/heats/{event}'
*/
getHeatsFor.head = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getHeatsFor.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getDrawFor
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:115
* @route '//judge.localhost/v2/{competition}/violation/draw/{serc}'
*/
export const getDrawFor = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDrawFor.url(args, options),
    method: 'get',
})

getDrawFor.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/violation/draw/{serc}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getDrawFor
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:115
* @route '//judge.localhost/v2/{competition}/violation/draw/{serc}'
*/
getDrawFor.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return getDrawFor.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getDrawFor
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:115
* @route '//judge.localhost/v2/{competition}/violation/draw/{serc}'
*/
getDrawFor.get = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDrawFor.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getDrawFor
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:115
* @route '//judge.localhost/v2/{competition}/violation/draw/{serc}'
*/
getDrawFor.head = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getDrawFor.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getEventRelatedCodes
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:120
* @route '//judge.localhost/v2/{competition}/violation/event-codes/{eventName}'
*/
export const getEventRelatedCodes = (args: { competition: number | { id: number }, eventName: string | number } | [competition: number | { id: number }, eventName: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEventRelatedCodes.url(args, options),
    method: 'get',
})

getEventRelatedCodes.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/violation/event-codes/{eventName}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getEventRelatedCodes
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:120
* @route '//judge.localhost/v2/{competition}/violation/event-codes/{eventName}'
*/
getEventRelatedCodes.url = (args: { competition: number | { id: number }, eventName: string | number } | [competition: number | { id: number }, eventName: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            eventName: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        eventName: args.eventName,
    }

    return getEventRelatedCodes.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{eventName}', parsedArgs.eventName.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getEventRelatedCodes
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:120
* @route '//judge.localhost/v2/{competition}/violation/event-codes/{eventName}'
*/
getEventRelatedCodes.get = (args: { competition: number | { id: number }, eventName: string | number } | [competition: number | { id: number }, eventName: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEventRelatedCodes.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getEventRelatedCodes
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:120
* @route '//judge.localhost/v2/{competition}/violation/event-codes/{eventName}'
*/
getEventRelatedCodes.head = (args: { competition: number | { id: number }, eventName: string | number } | [competition: number | { id: number }, eventName: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getEventRelatedCodes.url(args, options),
    method: 'head',
})

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

const ViolationController = { submissions, issue, submit, getHeatsFor, getDrawFor, getEventRelatedCodes, view }

export default ViolationController