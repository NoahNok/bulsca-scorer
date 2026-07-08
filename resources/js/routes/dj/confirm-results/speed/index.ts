import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::post
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:129
* @route '//judge.localhost/confirm/speed/{speed}'
*/
export const post = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '//judge.localhost/confirm/speed/{speed}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::post
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:129
* @route '//judge.localhost/confirm/speed/{speed}'
*/
post.url = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return post.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::post
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:129
* @route '//judge.localhost/confirm/speed/{speed}'
*/
post.post = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

const speed = {
    post: Object.assign(post, post),
}

export default speed