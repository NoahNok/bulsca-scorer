import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::timesIndex
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:21
* @route '//judge.localhost/speeds/{speed}/times'
*/
export const timesIndex = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: timesIndex.url(args, options),
    method: 'get',
})

timesIndex.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/speeds/{speed}/times',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::timesIndex
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:21
* @route '//judge.localhost/speeds/{speed}/times'
*/
timesIndex.url = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return timesIndex.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::timesIndex
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:21
* @route '//judge.localhost/speeds/{speed}/times'
*/
timesIndex.get = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: timesIndex.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::timesIndex
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:21
* @route '//judge.localhost/speeds/{speed}/times'
*/
timesIndex.head = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: timesIndex.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::timesJudge
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:29
* @route '//judge.localhost/speeds/{speed}/times/h/{heat}'
*/
export const timesJudge = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: timesJudge.url(args, options),
    method: 'get',
})

timesJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/speeds/{speed}/times/h/{heat}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::timesJudge
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:29
* @route '//judge.localhost/speeds/{speed}/times/h/{heat}'
*/
timesJudge.url = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions) => {
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

    return timesJudge.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace('{heat}', parsedArgs.heat.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::timesJudge
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:29
* @route '//judge.localhost/speeds/{speed}/times/h/{heat}'
*/
timesJudge.get = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: timesJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::timesJudge
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:29
* @route '//judge.localhost/speeds/{speed}/times/h/{heat}'
*/
timesJudge.head = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: timesJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::saveHeatTimes
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:68
* @route '//judge.localhost/speeds/{speed}/times/h/{heat}'
*/
export const saveHeatTimes = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveHeatTimes.url(args, options),
    method: 'post',
})

saveHeatTimes.definition = {
    methods: ["post"],
    url: '//judge.localhost/speeds/{speed}/times/h/{heat}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::saveHeatTimes
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:68
* @route '//judge.localhost/speeds/{speed}/times/h/{heat}'
*/
saveHeatTimes.url = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions) => {
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

    return saveHeatTimes.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace('{heat}', parsedArgs.heat.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::saveHeatTimes
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:68
* @route '//judge.localhost/speeds/{speed}/times/h/{heat}'
*/
saveHeatTimes.post = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveHeatTimes.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::oofIndex
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:181
* @route '//judge.localhost/speeds/{speed}/oof'
*/
export const oofIndex = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: oofIndex.url(args, options),
    method: 'get',
})

oofIndex.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/speeds/{speed}/oof',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::oofIndex
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:181
* @route '//judge.localhost/speeds/{speed}/oof'
*/
oofIndex.url = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return oofIndex.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::oofIndex
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:181
* @route '//judge.localhost/speeds/{speed}/oof'
*/
oofIndex.get = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: oofIndex.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::oofIndex
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:181
* @route '//judge.localhost/speeds/{speed}/oof'
*/
oofIndex.head = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: oofIndex.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::oofJudge
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:189
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
export const oofJudge = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: oofJudge.url(args, options),
    method: 'get',
})

oofJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/speeds/{speed}/oof/h/{heat}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::oofJudge
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:189
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
oofJudge.url = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions) => {
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

    return oofJudge.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace('{heat}', parsedArgs.heat.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::oofJudge
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:189
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
oofJudge.get = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: oofJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::oofJudge
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:189
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
oofJudge.head = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: oofJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::saveOofTimes
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:220
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
export const saveOofTimes = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveOofTimes.url(args, options),
    method: 'post',
})

saveOofTimes.definition = {
    methods: ["post"],
    url: '//judge.localhost/speeds/{speed}/oof/h/{heat}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::saveOofTimes
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:220
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
saveOofTimes.url = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions) => {
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

    return saveOofTimes.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace('{heat}', parsedArgs.heat.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SpeedJudgingController::saveOofTimes
* @see app/Http/Controllers/DigitalJudge/SpeedJudgingController.php:220
* @route '//judge.localhost/speeds/{speed}/oof/h/{heat}'
*/
saveOofTimes.post = (args: { speed: number | { id: number }, heat: string | number } | [speed: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveOofTimes.url(args, options),
    method: 'post',
})

const SpeedJudgingController = { timesIndex, timesJudge, saveHeatTimes, oofIndex, oofJudge, saveOofTimes }

export default SpeedJudgingController