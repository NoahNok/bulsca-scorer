import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::index
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:13
* @route '//judge.localhost/manage'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/manage',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::index
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:13
* @route '//judge.localhost/manage'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::index
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:13
* @route '//judge.localhost/manage'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::index
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:13
* @route '//judge.localhost/manage'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::manageSerc
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:18
* @route '//judge.localhost/manage/serc/{serc}'
*/
export const manageSerc = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manageSerc.url(args, options),
    method: 'get',
})

manageSerc.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/manage/serc/{serc}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::manageSerc
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:18
* @route '//judge.localhost/manage/serc/{serc}'
*/
manageSerc.url = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return manageSerc.definition.url
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::manageSerc
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:18
* @route '//judge.localhost/manage/serc/{serc}'
*/
manageSerc.get = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manageSerc.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::manageSerc
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:18
* @route '//judge.localhost/manage/serc/{serc}'
*/
manageSerc.head = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: manageSerc.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::manageSercPost
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:23
* @route '//judge.localhost/manage/serc/{serc}'
*/
export const manageSercPost = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: manageSercPost.url(args, options),
    method: 'post',
})

manageSercPost.definition = {
    methods: ["post"],
    url: '//judge.localhost/manage/serc/{serc}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::manageSercPost
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:23
* @route '//judge.localhost/manage/serc/{serc}'
*/
manageSercPost.url = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { serc: args }
    }

    if (Array.isArray(args)) {
        args = {
            serc: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        serc: args.serc,
    }

    return manageSercPost.definition.url
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::manageSercPost
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:23
* @route '//judge.localhost/manage/serc/{serc}'
*/
manageSercPost.post = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: manageSercPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::manageSpeed
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:0
* @route '//judge.localhost/manage/speed/{speed}'
*/
export const manageSpeed = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manageSpeed.url(args, options),
    method: 'get',
})

manageSpeed.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/manage/speed/{speed}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::manageSpeed
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:0
* @route '//judge.localhost/manage/speed/{speed}'
*/
manageSpeed.url = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { speed: args }
    }

    if (Array.isArray(args)) {
        args = {
            speed: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        speed: args.speed,
    }

    return manageSpeed.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::manageSpeed
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:0
* @route '//judge.localhost/manage/speed/{speed}'
*/
manageSpeed.get = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: manageSpeed.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::manageSpeed
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:0
* @route '//judge.localhost/manage/speed/{speed}'
*/
manageSpeed.head = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: manageSpeed.url(args, options),
    method: 'head',
})

const DJManageController = { index, manageSerc, manageSercPost, manageSpeed }

export default DJManageController