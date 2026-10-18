import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::issue
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:145
* @route '//judge.localhost/dq/issue'
*/
export const issue = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: issue.url(options),
    method: 'get',
})

issue.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/issue',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::issue
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:145
* @route '//judge.localhost/dq/issue'
*/
issue.url = (options?: RouteQueryOptions) => {
    return issue.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::issue
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:145
* @route '//judge.localhost/dq/issue'
*/
issue.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: issue.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::issue
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:145
* @route '//judge.localhost/dq/issue'
*/
issue.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: issue.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolveCode
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:151
* @route '//judge.localhost/dq/resolveCode/{code}'
*/
export const resolveCode = (args: { code: string | number } | [code: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resolveCode.url(args, options),
    method: 'get',
})

resolveCode.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/resolveCode/{code}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolveCode
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:151
* @route '//judge.localhost/dq/resolveCode/{code}'
*/
resolveCode.url = (args: { code: string | number } | [code: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { code: args }
    }

    if (Array.isArray(args)) {
        args = {
            code: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        code: args.code,
    }

    return resolveCode.definition.url
            .replace('{code}', parsedArgs.code.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolveCode
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:151
* @route '//judge.localhost/dq/resolveCode/{code}'
*/
resolveCode.get = (args: { code: string | number } | [code: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resolveCode.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolveCode
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:151
* @route '//judge.localhost/dq/resolveCode/{code}'
*/
resolveCode.head = (args: { code: string | number } | [code: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: resolveCode.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::submission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:167
* @route '//judge.localhost/dq/submission'
*/
export const submission = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submission.url(options),
    method: 'post',
})

submission.definition = {
    methods: ["post"],
    url: '//judge.localhost/dq/submission',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::submission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:167
* @route '//judge.localhost/dq/submission'
*/
submission.url = (options?: RouteQueryOptions) => {
    return submission.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::submission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:167
* @route '//judge.localhost/dq/submission'
*/
submission.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submission.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:215
* @route '//judge.localhost/dq/submission/{submission}/info'
*/
export const getSubmission = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSubmission.url(args, options),
    method: 'get',
})

getSubmission.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/submission/{submission}/info',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:215
* @route '//judge.localhost/dq/submission/{submission}/info'
*/
getSubmission.url = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { submission: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { submission: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            submission: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        submission: typeof args.submission === 'object'
        ? args.submission.id
        : args.submission,
    }

    return getSubmission.definition.url
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:215
* @route '//judge.localhost/dq/submission/{submission}/info'
*/
getSubmission.get = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSubmission.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:215
* @route '//judge.localhost/dq/submission/{submission}/info'
*/
getSubmission.head = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSubmission.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::submissionStatus
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:210
* @route '//judge.localhost/dq/submission/{submission}/status'
*/
export const submissionStatus = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: submissionStatus.url(args, options),
    method: 'get',
})

submissionStatus.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/submission/{submission}/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::submissionStatus
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:210
* @route '//judge.localhost/dq/submission/{submission}/status'
*/
submissionStatus.url = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { submission: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { submission: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            submission: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        submission: typeof args.submission === 'object'
        ? args.submission.id
        : args.submission,
    }

    return submissionStatus.definition.url
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::submissionStatus
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:210
* @route '//judge.localhost/dq/submission/{submission}/status'
*/
submissionStatus.get = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: submissionStatus.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::submissionStatus
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:210
* @route '//judge.localhost/dq/submission/{submission}/status'
*/
submissionStatus.head = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: submissionStatus.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getEventRelatedCodes
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:411
* @route '//judge.localhost/dq/event-codes/{event}'
*/
export const getEventRelatedCodes = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEventRelatedCodes.url(args, options),
    method: 'get',
})

getEventRelatedCodes.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/event-codes/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getEventRelatedCodes
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:411
* @route '//judge.localhost/dq/event-codes/{event}'
*/
getEventRelatedCodes.url = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { event: args }
    }

    if (Array.isArray(args)) {
        args = {
            event: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: args.event,
    }

    return getEventRelatedCodes.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getEventRelatedCodes
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:411
* @route '//judge.localhost/dq/event-codes/{event}'
*/
getEventRelatedCodes.get = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getEventRelatedCodes.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getEventRelatedCodes
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:411
* @route '//judge.localhost/dq/event-codes/{event}'
*/
getEventRelatedCodes.head = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getEventRelatedCodes.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::index
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:30
* @route '//judge.localhost/dq'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::index
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:30
* @route '//judge.localhost/dq'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::index
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:30
* @route '//judge.localhost/dq'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::index
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:30
* @route '//judge.localhost/dq'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::current
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:111
* @route '//judge.localhost/dq/current/{event}/{team}/{type}'
*/
export const current = (args: { event: string | number, team: string | number, type: string | number } | [event: string | number, team: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: current.url(args, options),
    method: 'get',
})

current.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/current/{event}/{team}/{type}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::current
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:111
* @route '//judge.localhost/dq/current/{event}/{team}/{type}'
*/
current.url = (args: { event: string | number, team: string | number, type: string | number } | [event: string | number, team: string | number, type: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            event: args[0],
            team: args[1],
            type: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        event: args.event,
        team: args.team,
        type: args.type,
    }

    return current.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace('{team}', parsedArgs.team.toString())
            .replace('{type}', parsedArgs.type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::current
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:111
* @route '//judge.localhost/dq/current/{event}/{team}/{type}'
*/
current.get = (args: { event: string | number, team: string | number, type: string | number } | [event: string | number, team: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: current.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::current
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:111
* @route '//judge.localhost/dq/current/{event}/{team}/{type}'
*/
current.head = (args: { event: string | number, team: string | number, type: string | number } | [event: string | number, team: string | number, type: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: current.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::submit
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:35
* @route '//judge.localhost/dq'
*/
export const submit = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

submit.definition = {
    methods: ["post"],
    url: '//judge.localhost/dq',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::submit
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:35
* @route '//judge.localhost/dq'
*/
submit.url = (options?: RouteQueryOptions) => {
    return submit.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::submit
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:35
* @route '//judge.localhost/dq'
*/
submit.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submit.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolve
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:220
* @route '//judge.localhost/dq/resolve'
*/
export const resolve = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resolve.url(options),
    method: 'get',
})

resolve.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/resolve',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolve
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:220
* @route '//judge.localhost/dq/resolve'
*/
resolve.url = (options?: RouteQueryOptions) => {
    return resolve.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolve
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:220
* @route '//judge.localhost/dq/resolve'
*/
resolve.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resolve.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolve
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:220
* @route '//judge.localhost/dq/resolve'
*/
resolve.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: resolve.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getNeedingResolving
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:281
* @route '//judge.localhost/dq/resolve/list'
*/
export const getNeedingResolving = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getNeedingResolving.url(options),
    method: 'get',
})

getNeedingResolving.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/resolve/list',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getNeedingResolving
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:281
* @route '//judge.localhost/dq/resolve/list'
*/
getNeedingResolving.url = (options?: RouteQueryOptions) => {
    return getNeedingResolving.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getNeedingResolving
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:281
* @route '//judge.localhost/dq/resolve/list'
*/
getNeedingResolving.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getNeedingResolving.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getNeedingResolving
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:281
* @route '//judge.localhost/dq/resolve/list'
*/
getNeedingResolving.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getNeedingResolving.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolveSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:228
* @route '//judge.localhost/dq/resolve/{submission}'
*/
export const resolveSubmission = (args: { submission: string | number } | [submission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resolveSubmission.url(args, options),
    method: 'post',
})

resolveSubmission.definition = {
    methods: ["post"],
    url: '//judge.localhost/dq/resolve/{submission}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolveSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:228
* @route '//judge.localhost/dq/resolve/{submission}'
*/
resolveSubmission.url = (args: { submission: string | number } | [submission: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { submission: args }
    }

    if (Array.isArray(args)) {
        args = {
            submission: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        submission: args.submission,
    }

    return resolveSubmission.definition.url
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolveSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:228
* @route '//judge.localhost/dq/resolve/{submission}'
*/
resolveSubmission.post = (args: { submission: string | number } | [submission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resolveSubmission.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getAccepted
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:297
* @route '//judge.localhost/dq/accepted'
*/
export const getAccepted = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAccepted.url(options),
    method: 'get',
})

getAccepted.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/accepted',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getAccepted
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:297
* @route '//judge.localhost/dq/accepted'
*/
getAccepted.url = (options?: RouteQueryOptions) => {
    return getAccepted.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getAccepted
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:297
* @route '//judge.localhost/dq/accepted'
*/
getAccepted.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAccepted.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::getAccepted
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:297
* @route '//judge.localhost/dq/accepted'
*/
getAccepted.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getAccepted.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::removeSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:331
* @route '//judge.localhost/dq/remove/{submission}'
*/
export const removeSubmission = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeSubmission.url(args, options),
    method: 'post',
})

removeSubmission.definition = {
    methods: ["post"],
    url: '//judge.localhost/dq/remove/{submission}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::removeSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:331
* @route '//judge.localhost/dq/remove/{submission}'
*/
removeSubmission.url = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { submission: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { submission: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            submission: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        submission: typeof args.submission === 'object'
        ? args.submission.id
        : args.submission,
    }

    return removeSubmission.definition.url
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::removeSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:331
* @route '//judge.localhost/dq/remove/{submission}'
*/
removeSubmission.post = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeSubmission.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::appealSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:353
* @route '//judge.localhost/dq/appeal/{submission}'
*/
export const appealSubmission = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: appealSubmission.url(args, options),
    method: 'post',
})

appealSubmission.definition = {
    methods: ["post"],
    url: '//judge.localhost/dq/appeal/{submission}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::appealSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:353
* @route '//judge.localhost/dq/appeal/{submission}'
*/
appealSubmission.url = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { submission: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { submission: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            submission: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        submission: typeof args.submission === 'object'
        ? args.submission.id
        : args.submission,
    }

    return appealSubmission.definition.url
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::appealSubmission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:353
* @route '//judge.localhost/dq/appeal/{submission}'
*/
appealSubmission.post = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: appealSubmission.url(args, options),
    method: 'post',
})

const DJDQController = { issue, resolveCode, submission, getSubmission, submissionStatus, getEventRelatedCodes, index, current, submit, resolve, getNeedingResolving, resolveSubmission, getAccepted, removeSubmission, appealSubmission }

export default DJDQController