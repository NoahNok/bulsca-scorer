import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import serc5c2b2c from './serc'
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
* @see \App\Http\Controllers\DigitalJudge\DJManageController::serc
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:18
* @route '//judge.localhost/manage/serc/{serc}'
*/
export const serc = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: serc.url(args, options),
    method: 'get',
})

serc.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/manage/serc/{serc}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::serc
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:18
* @route '//judge.localhost/manage/serc/{serc}'
*/
serc.url = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return serc.definition.url
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::serc
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:18
* @route '//judge.localhost/manage/serc/{serc}'
*/
serc.get = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: serc.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::serc
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:18
* @route '//judge.localhost/manage/serc/{serc}'
*/
serc.head = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: serc.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::speed
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:0
* @route '//judge.localhost/manage/speed/{speed}'
*/
export const speed = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: speed.url(args, options),
    method: 'get',
})

speed.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/manage/speed/{speed}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::speed
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:0
* @route '//judge.localhost/manage/speed/{speed}'
*/
speed.url = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return speed.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::speed
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:0
* @route '//judge.localhost/manage/speed/{speed}'
*/
speed.get = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: speed.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::speed
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:0
* @route '//judge.localhost/manage/speed/{speed}'
*/
speed.head = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: speed.url(args, options),
    method: 'head',
})

const manage = {
    index: Object.assign(index, index),
    serc: Object.assign(serc, serc5c2b2c),
    speed: Object.assign(speed, speed),
}

export default manage