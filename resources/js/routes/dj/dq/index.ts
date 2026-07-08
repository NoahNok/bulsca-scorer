import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import submission4910ce from './submission'
import index801d9f from './index'
import resolve36a22e from './resolve'
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
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:150
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
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:150
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
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:150
* @route '//judge.localhost/dq/resolveCode/{code}'
*/
resolveCode.get = (args: { code: string | number } | [code: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resolveCode.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolveCode
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:150
* @route '//judge.localhost/dq/resolveCode/{code}'
*/
resolveCode.head = (args: { code: string | number } | [code: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: resolveCode.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::submission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:166
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
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:166
* @route '//judge.localhost/dq/submission'
*/
submission.url = (options?: RouteQueryOptions) => {
    return submission.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::submission
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:166
* @route '//judge.localhost/dq/submission'
*/
submission.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submission.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::eventCodes
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:401
* @route '//judge.localhost/dq/event-codes/{event}'
*/
export const eventCodes = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: eventCodes.url(args, options),
    method: 'get',
})

eventCodes.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/event-codes/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::eventCodes
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:401
* @route '//judge.localhost/dq/event-codes/{event}'
*/
eventCodes.url = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return eventCodes.definition.url
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::eventCodes
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:401
* @route '//judge.localhost/dq/event-codes/{event}'
*/
eventCodes.get = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: eventCodes.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::eventCodes
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:401
* @route '//judge.localhost/dq/event-codes/{event}'
*/
eventCodes.head = (args: { event: string | number } | [event: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: eventCodes.url(args, options),
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
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolve
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:216
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
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:216
* @route '//judge.localhost/dq/resolve'
*/
resolve.url = (options?: RouteQueryOptions) => {
    return resolve.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolve
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:216
* @route '//judge.localhost/dq/resolve'
*/
resolve.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resolve.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolve
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:216
* @route '//judge.localhost/dq/resolve'
*/
resolve.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: resolve.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::accepted
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:289
* @route '//judge.localhost/dq/accepted'
*/
export const accepted = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: accepted.url(options),
    method: 'get',
})

accepted.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/accepted',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::accepted
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:289
* @route '//judge.localhost/dq/accepted'
*/
accepted.url = (options?: RouteQueryOptions) => {
    return accepted.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::accepted
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:289
* @route '//judge.localhost/dq/accepted'
*/
accepted.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: accepted.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::accepted
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:289
* @route '//judge.localhost/dq/accepted'
*/
accepted.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: accepted.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::remove
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:323
* @route '//judge.localhost/dq/remove/{submission}'
*/
export const remove = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: remove.url(args, options),
    method: 'post',
})

remove.definition = {
    methods: ["post"],
    url: '//judge.localhost/dq/remove/{submission}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::remove
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:323
* @route '//judge.localhost/dq/remove/{submission}'
*/
remove.url = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return remove.definition.url
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::remove
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:323
* @route '//judge.localhost/dq/remove/{submission}'
*/
remove.post = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: remove.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::appeal
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:343
* @route '//judge.localhost/dq/appeal/{submission}'
*/
export const appeal = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: appeal.url(args, options),
    method: 'post',
})

appeal.definition = {
    methods: ["post"],
    url: '//judge.localhost/dq/appeal/{submission}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::appeal
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:343
* @route '//judge.localhost/dq/appeal/{submission}'
*/
appeal.url = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return appeal.definition.url
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::appeal
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:343
* @route '//judge.localhost/dq/appeal/{submission}'
*/
appeal.post = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: appeal.url(args, options),
    method: 'post',
})

const dq = {
    issue: Object.assign(issue, issue),
    resolveCode: Object.assign(resolveCode, resolveCode),
    submission: Object.assign(submission, submission4910ce),
    eventCodes: Object.assign(eventCodes, eventCodes),
    index: Object.assign(index, index801d9f),
    current: Object.assign(current, current),
    resolve: Object.assign(resolve, resolve36a22e),
    accepted: Object.assign(accepted, accepted),
    remove: Object.assign(remove, remove),
    appeal: Object.assign(appeal, appeal),
}

export default dq