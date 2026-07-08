import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::index
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:178
* @route '//judge.localhost/speeds/{speed}/oof'
*/
export const index = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/speeds/{speed}/oof',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::index
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:178
* @route '//judge.localhost/speeds/{speed}/oof'
*/
index.url = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::index
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:178
* @route '//judge.localhost/speeds/{speed}/oof'
*/
index.get = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::index
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:178
* @route '//judge.localhost/speeds/{speed}/oof'
*/
index.head = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::judge
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:184
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
export const judge = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: judge.url(args, options),
    method: 'get',
})

judge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/speeds/{speed}/oof/h/{heat}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::judge
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:184
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
judge.url = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            speed: args[0],
            heat: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        speed: typeof args.speed === 'object'
        ? args.speed.id
        : args.speed,
        heat: args.heat,
    }

    return judge.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace('{heat}', parsedArgs.heat.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::judge
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:184
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
judge.get = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: judge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::judge
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:184
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
judge.head = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: judge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::judgePost
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:214
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
export const judgePost = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: judgePost.url(args, options),
    method: 'post',
})

judgePost.definition = {
    methods: ["post"],
    url: '//judge.localhost/speeds/{speed}/oof/h/{heat}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::judgePost
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:214
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
judgePost.url = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            speed: args[0],
            heat: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        speed: typeof args.speed === 'object'
        ? args.speed.id
        : args.speed,
        heat: args.heat,
    }

    return judgePost.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace('{heat}', parsedArgs.heat.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::judgePost
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:214
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
judgePost.post = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: judgePost.url(args, options),
    method: 'post',
})

const oof = {
    index: Object.assign(index, index),
    judge: Object.assign(judge, judge),
    judgePost: Object.assign(judgePost, judgePost),
}

export default oof