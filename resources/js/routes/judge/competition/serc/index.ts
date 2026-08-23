import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
import mark from './mark'
import overallNotes from './overall-notes'
/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::confirm
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:25
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm/{judge}'
*/
export const confirm = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirm.url(args, options),
    method: 'get',
})

confirm.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/confirm/{judge}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::confirm
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:25
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm/{judge}'
*/
confirm.url = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return confirm.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::confirm
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:25
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm/{judge}'
*/
confirm.get = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirm.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::confirm
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:25
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm/{judge}'
*/
confirm.head = (args: { competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirm.url(args, options),
    method: 'head',
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
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::previousMarks
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:356
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/previous-marks/{judge_id}'
*/
export const previousMarks = (args: { competition: string | number, serc: string | number, judge_id: string | number } | [competition: string | number, serc: string | number, judge_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: previousMarks.url(args, options),
    method: 'get',
})

previousMarks.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/mark/previous-marks/{judge_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::previousMarks
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:356
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/previous-marks/{judge_id}'
*/
previousMarks.url = (args: { competition: string | number, serc: string | number, judge_id: string | number } | [competition: string | number, serc: string | number, judge_id: string | number ], options?: RouteQueryOptions) => {
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

    return previousMarks.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{judge_id}', parsedArgs.judge_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::previousMarks
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:356
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/previous-marks/{judge_id}'
*/
previousMarks.get = (args: { competition: string | number, serc: string | number, judge_id: string | number } | [competition: string | number, serc: string | number, judge_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: previousMarks.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::previousMarks
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:356
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/previous-marks/{judge_id}'
*/
previousMarks.head = (args: { competition: string | number, serc: string | number, judge_id: string | number } | [competition: string | number, serc: string | number, judge_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: previousMarks.url(args, options),
    method: 'head',
})

const serc = {
    mark: Object.assign(mark, mark),
    previousMarks: Object.assign(previousMarks, previousMarks),
    overallNotes: Object.assign(overallNotes, overallNotes),
}

export default serc