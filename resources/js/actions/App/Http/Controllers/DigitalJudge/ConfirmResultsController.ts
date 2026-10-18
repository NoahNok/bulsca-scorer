import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::serc
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:36
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results'
*/
export const serc = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: serc.url(args, options),
    method: 'get',
})

serc.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::serc
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:36
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results'
*/
serc.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return serc.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::serc
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:36
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results'
*/
serc.get = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: serc.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::serc
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:36
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results'
*/
serc.head = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: serc.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::storeSerc
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:63
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results'
*/
export const storeSerc = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeSerc.url(args, options),
    method: 'post',
})

storeSerc.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::storeSerc
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:63
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results'
*/
storeSerc.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return storeSerc.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::storeSerc
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:63
* @route '//judge.localhost/v2/{competition}/serc/{serc}/confirm-results'
*/
storeSerc.post = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeSerc.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::event
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:68
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
export const event = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: event.url(args, options),
    method: 'get',
})

event.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/event/{event}/confirm-results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::event
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:68
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
event.url = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return event.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::event
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:68
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
event.get = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: event.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::event
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:68
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
event.head = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: event.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::storeEvent
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:83
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
export const storeEvent = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeEvent.url(args, options),
    method: 'post',
})

storeEvent.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/event/{event}/confirm-results',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::storeEvent
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:83
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
storeEvent.url = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return storeEvent.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::storeEvent
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:83
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
storeEvent.post = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeEvent.url(args, options),
    method: 'post',
})

const ConfirmResultsController = { serc, storeSerc, event, storeEvent }

export default ConfirmResultsController