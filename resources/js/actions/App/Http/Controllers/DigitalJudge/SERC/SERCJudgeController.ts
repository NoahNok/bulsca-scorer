import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::confirmJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:25
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm/{judge}'
*/
export const confirmJudge = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmJudge.url(args, options),
    method: 'get',
})

confirmJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/confirm/{judge}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::confirmJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:25
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm/{judge}'
*/
confirmJudge.url = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
            judge: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return confirmJudge.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::confirmJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:25
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm/{judge}'
*/
confirmJudge.get = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::confirmJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:25
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm/{judge}'
*/
confirmJudge.head = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirmJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::confirmJudgePost
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:45
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm'
*/
export const confirmJudgePost = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmJudgePost.url(args, options),
    method: 'post',
})

confirmJudgePost.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/confirm',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::confirmJudgePost
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:45
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm'
*/
confirmJudgePost.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return confirmJudgePost.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::confirmJudgePost
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:45
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm'
*/
confirmJudgePost.post = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmJudgePost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::addJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:131
* @route '//judge.localhost/v2/{competition}/serc/{serc}/add-judge'
*/
export const addJudge = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: addJudge.url(args, options),
    method: 'get',
})

addJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/add-judge',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::addJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:131
* @route '//judge.localhost/v2/{competition}/serc/{serc}/add-judge'
*/
addJudge.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return addJudge.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::addJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:131
* @route '//judge.localhost/v2/{competition}/serc/{serc}/add-judge'
*/
addJudge.get = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: addJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::addJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:131
* @route '//judge.localhost/v2/{competition}/serc/{serc}/add-judge'
*/
addJudge.head = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: addJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::attachJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:157
* @route '//judge.localhost/v2/{competition}/serc/{serc}/attach-judge/{judge}'
*/
export const attachJudge = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: attachJudge.url(args, options),
    method: 'get',
})

attachJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/attach-judge/{judge}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::attachJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:157
* @route '//judge.localhost/v2/{competition}/serc/{serc}/attach-judge/{judge}'
*/
attachJudge.url = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
            judge: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return attachJudge.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::attachJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:157
* @route '//judge.localhost/v2/{competition}/serc/{serc}/attach-judge/{judge}'
*/
attachJudge.get = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: attachJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::attachJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:157
* @route '//judge.localhost/v2/{competition}/serc/{serc}/attach-judge/{judge}'
*/
attachJudge.head = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: attachJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::detachJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:169
* @route '//judge.localhost/v2/{competition}/serc/{serc}/detach-judge/{judge}'
*/
export const detachJudge = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detachJudge.url(args, options),
    method: 'get',
})

detachJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/detach-judge/{judge}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::detachJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:169
* @route '//judge.localhost/v2/{competition}/serc/{serc}/detach-judge/{judge}'
*/
detachJudge.url = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
            judge: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return detachJudge.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::detachJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:169
* @route '//judge.localhost/v2/{competition}/serc/{serc}/detach-judge/{judge}'
*/
detachJudge.get = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detachJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::detachJudge
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:169
* @route '//judge.localhost/v2/{competition}/serc/{serc}/detach-judge/{judge}'
*/
detachJudge.head = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detachJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::selectTank
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:60
* @route '//judge.localhost/v2/{competition}/serc/{serc}/select-tank'
*/
export const selectTank = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectTank.url(args, options),
    method: 'get',
})

selectTank.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/select-tank',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::selectTank
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:60
* @route '//judge.localhost/v2/{competition}/serc/{serc}/select-tank'
*/
selectTank.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return selectTank.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::selectTank
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:60
* @route '//judge.localhost/v2/{competition}/serc/{serc}/select-tank'
*/
selectTank.get = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectTank.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::selectTank
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:60
* @route '//judge.localhost/v2/{competition}/serc/{serc}/select-tank'
*/
selectTank.head = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: selectTank.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::setTank
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:70
* @route '//judge.localhost/v2/{competition}/serc/{serc}/select-tank/{tank}'
*/
export const setTank = (args: { competition: number | { id: number }, serc: number | { id: number }, tank: string | number } | [competition: number | { id: number }, serc: number | { id: number }, tank: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setTank.url(args, options),
    method: 'get',
})

setTank.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/select-tank/{tank}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::setTank
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:70
* @route '//judge.localhost/v2/{competition}/serc/{serc}/select-tank/{tank}'
*/
setTank.url = (args: { competition: number | { id: number }, serc: number | { id: number }, tank: string | number } | [competition: number | { id: number }, serc: number | { id: number }, tank: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
            tank: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
        tank: args.tank,
    }

    return setTank.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{tank}', parsedArgs.tank.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::setTank
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:70
* @route '//judge.localhost/v2/{competition}/serc/{serc}/select-tank/{tank}'
*/
setTank.get = (args: { competition: number | { id: number }, serc: number | { id: number }, tank: string | number } | [competition: number | { id: number }, serc: number | { id: number }, tank: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setTank.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::setTank
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:70
* @route '//judge.localhost/v2/{competition}/serc/{serc}/select-tank/{tank}'
*/
setTank.head = (args: { competition: number | { id: number }, serc: number | { id: number }, tank: string | number } | [competition: number | { id: number }, serc: number | { id: number }, tank: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: setTank.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::home
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:77
* @route '//judge.localhost/v2/{competition}/serc/{serc}'
*/
export const home = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(args, options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::home
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:77
* @route '//judge.localhost/v2/{competition}/serc/{serc}'
*/
home.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return home.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::home
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:77
* @route '//judge.localhost/v2/{competition}/serc/{serc}'
*/
home.get = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::home
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:77
* @route '//judge.localhost/v2/{competition}/serc/{serc}'
*/
home.head = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::nextEntityToMark
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:179
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/next'
*/
export const nextEntityToMark = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: nextEntityToMark.url(args, options),
    method: 'get',
})

nextEntityToMark.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/mark/next',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::nextEntityToMark
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:179
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/next'
*/
nextEntityToMark.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return nextEntityToMark.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::nextEntityToMark
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:179
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/next'
*/
nextEntityToMark.get = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: nextEntityToMark.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::nextEntityToMark
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:179
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/next'
*/
nextEntityToMark.head = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: nextEntityToMark.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::markEntity
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:224
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
export const markEntity = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: markEntity.url(args, options),
    method: 'get',
})

markEntity.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::markEntity
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:224
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
markEntity.url = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
            entity_id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
        entity_id: args.entity_id,
    }

    return markEntity.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::markEntity
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:224
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
markEntity.get = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: markEntity.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::markEntity
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:224
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
markEntity.head = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: markEntity.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::storeEntityMarks
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:273
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
export const storeEntityMarks = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeEntityMarks.url(args, options),
    method: 'post',
})

storeEntityMarks.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::storeEntityMarks
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:273
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
storeEntityMarks.url = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
            entity_id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
        entity_id: args.entity_id,
    }

    return storeEntityMarks.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::storeEntityMarks
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:273
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
storeEntityMarks.post = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeEntityMarks.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::getJudgeNotes
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:337
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/notes'
*/
export const getJudgeNotes = (args: { competition: string | number, serc: string | number } | [competition: string | number, serc: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getJudgeNotes.url(args, options),
    method: 'get',
})

getJudgeNotes.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/mark/notes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::getJudgeNotes
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:337
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/notes'
*/
getJudgeNotes.url = (args: { competition: string | number, serc: string | number } | [competition: string | number, serc: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: args.competition,
        serc: args.serc,
    }

    return getJudgeNotes.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::getJudgeNotes
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:337
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/notes'
*/
getJudgeNotes.get = (args: { competition: string | number, serc: string | number } | [competition: string | number, serc: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getJudgeNotes.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::getJudgeNotes
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:337
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/notes'
*/
getJudgeNotes.head = (args: { competition: string | number, serc: string | number } | [competition: string | number, serc: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getJudgeNotes.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::getPreviousMarks
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:356
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/previous-marks/{judge_id}'
*/
export const getPreviousMarks = (args: { competition: string | number, serc: string | number, judge_id: string | number } | [competition: string | number, serc: string | number, judge_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPreviousMarks.url(args, options),
    method: 'get',
})

getPreviousMarks.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/mark/previous-marks/{judge_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::getPreviousMarks
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:356
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/previous-marks/{judge_id}'
*/
getPreviousMarks.url = (args: { competition: string | number, serc: string | number, judge_id: string | number } | [competition: string | number, serc: string | number, judge_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
            judge_id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: args.competition,
        serc: args.serc,
        judge_id: args.judge_id,
    }

    return getPreviousMarks.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{judge_id}', parsedArgs.judge_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::getPreviousMarks
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:356
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/previous-marks/{judge_id}'
*/
getPreviousMarks.get = (args: { competition: string | number, serc: string | number, judge_id: string | number } | [competition: string | number, serc: string | number, judge_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPreviousMarks.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::getPreviousMarks
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:356
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/previous-marks/{judge_id}'
*/
getPreviousMarks.head = (args: { competition: string | number, serc: string | number, judge_id: string | number } | [competition: string | number, serc: string | number, judge_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getPreviousMarks.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::storeOverallNotes
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:319
* @route '//judge.localhost/v2/{competition}/serc/{serc}/overall-notes'
*/
export const storeOverallNotes = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeOverallNotes.url(args, options),
    method: 'post',
})

storeOverallNotes.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/overall-notes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::storeOverallNotes
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:319
* @route '//judge.localhost/v2/{competition}/serc/{serc}/overall-notes'
*/
storeOverallNotes.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return storeOverallNotes.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::storeOverallNotes
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:319
* @route '//judge.localhost/v2/{competition}/serc/{serc}/overall-notes'
*/
storeOverallNotes.post = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeOverallNotes.url(args, options),
    method: 'post',
})

const SERCJudgeController = { confirmJudge, confirmJudgePost, addJudge, attachJudge, detachJudge, selectTank, setTank, home, nextEntityToMark, markEntity, storeEntityMarks, getJudgeNotes, getPreviousMarks, storeOverallNotes }

export default SERCJudgeController