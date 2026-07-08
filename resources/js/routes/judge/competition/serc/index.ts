import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::confirm
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:151
* @route '//judge.localhost/new/{competition}/serc/confirm/{judge}'
*/
export const confirm = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirm.url(args, options),
    method: 'get',
})

confirm.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc/confirm/{judge}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::confirm
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:151
* @route '//judge.localhost/new/{competition}/serc/confirm/{judge}'
*/
confirm.url = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            judge: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return confirm.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::confirm
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:151
* @route '//judge.localhost/new/{competition}/serc/confirm/{judge}'
*/
confirm.get = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirm.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::confirm
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:151
* @route '//judge.localhost/new/{competition}/serc/confirm/{judge}'
*/
confirm.head = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirm.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::addJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:255
* @route '//judge.localhost/new/{competition}/serc/add-judge'
*/
export const addJudge = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: addJudge.url(args, options),
    method: 'get',
})

addJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc/add-judge',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::addJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:255
* @route '//judge.localhost/new/{competition}/serc/add-judge'
*/
addJudge.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return addJudge.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::addJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:255
* @route '//judge.localhost/new/{competition}/serc/add-judge'
*/
addJudge.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: addJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::addJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:255
* @route '//judge.localhost/new/{competition}/serc/add-judge'
*/
addJudge.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: addJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::attachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:281
* @route '//judge.localhost/new/{competition}/serc/attach-judge/{judge}'
*/
export const attachJudge = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: attachJudge.url(args, options),
    method: 'get',
})

attachJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc/attach-judge/{judge}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::attachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:281
* @route '//judge.localhost/new/{competition}/serc/attach-judge/{judge}'
*/
attachJudge.url = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            judge: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return attachJudge.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::attachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:281
* @route '//judge.localhost/new/{competition}/serc/attach-judge/{judge}'
*/
attachJudge.get = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: attachJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::attachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:281
* @route '//judge.localhost/new/{competition}/serc/attach-judge/{judge}'
*/
attachJudge.head = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: attachJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::detachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:293
* @route '//judge.localhost/new/{competition}/serc/detach-judge/{judge}'
*/
export const detachJudge = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detachJudge.url(args, options),
    method: 'get',
})

detachJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc/detach-judge/{judge}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::detachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:293
* @route '//judge.localhost/new/{competition}/serc/detach-judge/{judge}'
*/
detachJudge.url = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            judge: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return detachJudge.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::detachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:293
* @route '//judge.localhost/new/{competition}/serc/detach-judge/{judge}'
*/
detachJudge.get = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detachJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::detachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:293
* @route '//judge.localhost/new/{competition}/serc/detach-judge/{judge}'
*/
detachJudge.head = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detachJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::selectTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:186
* @route '//judge.localhost/new/{competition}/serc/select-tank'
*/
export const selectTank = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectTank.url(args, options),
    method: 'get',
})

selectTank.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc/select-tank',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::selectTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:186
* @route '//judge.localhost/new/{competition}/serc/select-tank'
*/
selectTank.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return selectTank.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::selectTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:186
* @route '//judge.localhost/new/{competition}/serc/select-tank'
*/
selectTank.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectTank.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::selectTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:186
* @route '//judge.localhost/new/{competition}/serc/select-tank'
*/
selectTank.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: selectTank.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::setTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:194
* @route '//judge.localhost/new/{competition}/serc/select-tank/{tank}'
*/
export const setTank = (args: { competition: number | { id: number }, tank: string | number } | [competition: number | { id: number }, tank: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setTank.url(args, options),
    method: 'get',
})

setTank.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc/select-tank/{tank}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::setTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:194
* @route '//judge.localhost/new/{competition}/serc/select-tank/{tank}'
*/
setTank.url = (args: { competition: number | { id: number }, tank: string | number } | [competition: number | { id: number }, tank: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            tank: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        tank: args.tank,
    }

    return setTank.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{tank}', parsedArgs.tank.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::setTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:194
* @route '//judge.localhost/new/{competition}/serc/select-tank/{tank}'
*/
setTank.get = (args: { competition: number | { id: number }, tank: string | number } | [competition: number | { id: number }, tank: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setTank.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::setTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:194
* @route '//judge.localhost/new/{competition}/serc/select-tank/{tank}'
*/
setTank.head = (args: { competition: number | { id: number }, tank: string | number } | [competition: number | { id: number }, tank: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: setTank.url(args, options),
    method: 'head',
})

