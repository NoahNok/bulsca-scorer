import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getHeats
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:100
* @route '//judge.localhost/v2/{competition}/violation/heats/{event}'
*/
export const getHeats = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getHeats.url(args, options),
    method: 'get',
})

getHeats.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/violation/heats/{event}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getHeats
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:100
* @route '//judge.localhost/v2/{competition}/violation/heats/{event}'
*/
getHeats.url = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return getHeats.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getHeats
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:100
* @route '//judge.localhost/v2/{competition}/violation/heats/{event}'
*/
getHeats.get = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getHeats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getHeats
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:100
* @route '//judge.localhost/v2/{competition}/violation/heats/{event}'
*/
getHeats.head = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getHeats.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getDraw
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:115
* @route '//judge.localhost/v2/{competition}/violation/draw/{serc}'
*/
export const getDraw = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDraw.url(args, options),
    method: 'get',
})

getDraw.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/violation/draw/{serc}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getDraw
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:115
* @route '//judge.localhost/v2/{competition}/violation/draw/{serc}'
*/
getDraw.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return getDraw.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getDraw
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:115
* @route '//judge.localhost/v2/{competition}/violation/draw/{serc}'
*/
getDraw.get = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getDraw.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getDraw
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:115
* @route '//judge.localhost/v2/{competition}/violation/draw/{serc}'
*/
getDraw.head = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getDraw.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getCodes
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:120
* @route '//judge.localhost/v2/{competition}/violation/event-codes/{eventName}'
*/
export const getCodes = (args: { competition: number | { id: number }, eventName: string | number } | [competition: number | { id: number }, eventName: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCodes.url(args, options),
    method: 'get',
})

getCodes.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/violation/event-codes/{eventName}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getCodes
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:120
* @route '//judge.localhost/v2/{competition}/violation/event-codes/{eventName}'
*/
getCodes.url = (args: { competition: number | { id: number }, eventName: string | number } | [competition: number | { id: number }, eventName: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            eventName: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        eventName: args.eventName,
    }

    return getCodes.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{eventName}', parsedArgs.eventName.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getCodes
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:120
* @route '//judge.localhost/v2/{competition}/violation/event-codes/{eventName}'
*/
getCodes.get = (args: { competition: number | { id: number }, eventName: string | number } | [competition: number | { id: number }, eventName: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCodes.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Violation\ViolationController::getCodes
* @see app/Http/Controllers/DigitalJudge/Violation/ViolationController.php:120
* @route '//judge.localhost/v2/{competition}/violation/event-codes/{eventName}'
*/
getCodes.head = (args: { competition: number | { id: number }, eventName: string | number } | [competition: number | { id: number }, eventName: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCodes.url(args, options),
    method: 'head',
})

const issue = {
    getHeats: Object.assign(getHeats, getHeats),
    getDraw: Object.assign(getDraw, getDraw),
    getCodes: Object.assign(getCodes, getCodes),
}

export default issue