import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::info
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:211
* @route '//judge.localhost/dq/submission/{submission}/info'
*/
export const info = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: info.url(args, options),
    method: 'get',
})

info.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/submission/{submission}/info',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::info
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:211
* @route '//judge.localhost/dq/submission/{submission}/info'
*/
info.url = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return info.definition.url
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::info
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:211
* @route '//judge.localhost/dq/submission/{submission}/info'
*/
info.get = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: info.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::info
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:211
* @route '//judge.localhost/dq/submission/{submission}/info'
*/
info.head = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: info.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::status
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:206
* @route '//judge.localhost/dq/submission/{submission}/status'
*/
export const status = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

status.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/submission/{submission}/status',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::status
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:206
* @route '//judge.localhost/dq/submission/{submission}/status'
*/
status.url = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return status.definition.url
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::status
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:206
* @route '//judge.localhost/dq/submission/{submission}/status'
*/
status.get = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::status
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:206
* @route '//judge.localhost/dq/submission/{submission}/status'
*/
status.head = (args: { submission: number | { id: number } } | [submission: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolve
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:221
* @route '//judge.localhost/dq/resolve/{submission}'
*/
export const resolve = (args: { submission: string | number } | [submission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resolve.url(args, options),
    method: 'post',
})

resolve.definition = {
    methods: ["post"],
    url: '//judge.localhost/dq/resolve/{submission}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolve
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:221
* @route '//judge.localhost/dq/resolve/{submission}'
*/
resolve.url = (args: { submission: string | number } | [submission: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return resolve.definition.url
            .replace('{submission}', parsedArgs.submission.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::resolve
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:221
* @route '//judge.localhost/dq/resolve/{submission}'
*/
resolve.post = (args: { submission: string | number } | [submission: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resolve.url(args, options),
    method: 'post',
})

const submission = {
    info: Object.assign(info, info),
    status: Object.assign(status, status),
    resolve: Object.assign(resolve, resolve),
}

export default submission