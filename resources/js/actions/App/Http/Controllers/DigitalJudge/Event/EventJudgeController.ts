import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::selectTimeHeat
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:19
* @route '//judge.localhost/v2/{competition}/event/{event}/time/select-heat'
*/
export const selectTimeHeat = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectTimeHeat.url(args, options),
    method: 'get',
})

selectTimeHeat.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/event/{event}/time/select-heat',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::selectTimeHeat
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:19
* @route '//judge.localhost/v2/{competition}/event/{event}/time/select-heat'
*/
selectTimeHeat.url = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            event: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return selectTimeHeat.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::selectTimeHeat
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:19
* @route '//judge.localhost/v2/{competition}/event/{event}/time/select-heat'
*/
selectTimeHeat.get = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectTimeHeat.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::selectTimeHeat
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:19
* @route '//judge.localhost/v2/{competition}/event/{event}/time/select-heat'
*/
selectTimeHeat.head = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: selectTimeHeat.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::markTime
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:52
* @route '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}'
*/
export const markTime = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: markTime.url(args, options),
    method: 'get',
})

markTime.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::markTime
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:52
* @route '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}'
*/
markTime.url = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            event: args[1],
            heat: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
        heat: args.heat,
    }

    return markTime.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace('{heat}', parsedArgs.heat.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::markTime
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:52
* @route '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}'
*/
markTime.get = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: markTime.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::markTime
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:52
* @route '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}'
*/
markTime.head = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: markTime.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::storeTime
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:82
* @route '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}'
*/
export const storeTime = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeTime.url(args, options),
    method: 'post',
})

storeTime.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::storeTime
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:82
* @route '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}'
*/
storeTime.url = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            event: args[1],
            heat: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
        heat: args.heat,
    }

    return storeTime.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace('{heat}', parsedArgs.heat.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::storeTime
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:82
* @route '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}'
*/
storeTime.post = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeTime.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::selectOOFHeat
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:149
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/select-heat'
*/
export const selectOOFHeat = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectOOFHeat.url(args, options),
    method: 'get',
})

selectOOFHeat.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/event/{event}/oof/select-heat',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::selectOOFHeat
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:149
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/select-heat'
*/
selectOOFHeat.url = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            event: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return selectOOFHeat.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::selectOOFHeat
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:149
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/select-heat'
*/
selectOOFHeat.get = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectOOFHeat.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::selectOOFHeat
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:149
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/select-heat'
*/
selectOOFHeat.head = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: selectOOFHeat.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::markOOF
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:182
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}'
*/
export const markOOF = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: markOOF.url(args, options),
    method: 'get',
})

markOOF.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::markOOF
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:182
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}'
*/
markOOF.url = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            event: args[1],
            heat: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
        heat: args.heat,
    }

    return markOOF.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace('{heat}', parsedArgs.heat.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::markOOF
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:182
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}'
*/
markOOF.get = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: markOOF.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::markOOF
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:182
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}'
*/
markOOF.head = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: markOOF.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::storeOOF
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:199
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}'
*/
export const storeOOF = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeOOF.url(args, options),
    method: 'post',
})

storeOOF.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::storeOOF
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:199
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}'
*/
storeOOF.url = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            event: args[1],
            heat: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
        heat: args.heat,
    }

    return storeOOF.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace('{heat}', parsedArgs.heat.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::storeOOF
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:199
* @route '//judge.localhost/v2/{competition}/event/{event}/oof/mark/{heat}'
*/
storeOOF.post = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeOOF.url(args, options),
    method: 'post',
})

const EventJudgeController = { selectTimeHeat, markTime, storeTime, selectOOFHeat, markOOF, storeOOF }

export default EventJudgeController