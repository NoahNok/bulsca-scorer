import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import speedCb6cfd from './speed'
/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::post
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:113
* @route '//judge.localhost/confirm/serc/{serc}'
*/
export const post = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '//judge.localhost/confirm/serc/{serc}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::post
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:113
* @route '//judge.localhost/confirm/serc/{serc}'
*/
post.url = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { serc: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { serc: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            serc: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return post.definition.url
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::post
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:113
* @route '//judge.localhost/confirm/serc/{serc}'
*/
post.post = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::speed
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:123
* @route '//judge.localhost/confirm/speed/{speed}'
*/
export const speed = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: speed.url(args, options),
    method: 'get',
})

speed.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/confirm/speed/{speed}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::speed
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:123
* @route '//judge.localhost/confirm/speed/{speed}'
*/
speed.url = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { speed: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { speed: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            speed: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        speed: typeof args.speed === 'object'
        ? args.speed.id
        : args.speed,
    }

    return speed.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::speed
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:123
* @route '//judge.localhost/confirm/speed/{speed}'
*/
speed.get = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: speed.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::speed
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:123
* @route '//judge.localhost/confirm/speed/{speed}'
*/
speed.head = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: speed.url(args, options),
    method: 'head',
})

const confirmResults = {
    post: Object.assign(post, post),
    speed: Object.assign(speed, speedCb6cfd),
}

export default confirmResults