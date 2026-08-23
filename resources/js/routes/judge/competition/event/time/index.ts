import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
import mark2a6e27 from './mark'
/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::selectHeat
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:17
* @route '//judge.localhost/v2/{competition}/event/{event}/time/select-heat'
*/
export const selectHeat = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectHeat.url(args, options),
    method: 'get',
})

selectHeat.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/event/{event}/time/select-heat',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::selectHeat
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:17
* @route '//judge.localhost/v2/{competition}/event/{event}/time/select-heat'
*/
selectHeat.url = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return selectHeat.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::selectHeat
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:17
* @route '//judge.localhost/v2/{competition}/event/{event}/time/select-heat'
*/
selectHeat.get = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectHeat.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::selectHeat
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:17
* @route '//judge.localhost/v2/{competition}/event/{event}/time/select-heat'
*/
selectHeat.head = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: selectHeat.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::mark
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:52
* @route '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}'
*/
export const mark = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mark.url(args, options),
    method: 'get',
})

mark.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::mark
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:52
* @route '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}'
*/
mark.url = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions) => {
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

    return mark.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace('{heat}', parsedArgs.heat.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::mark
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:52
* @route '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}'
*/
mark.get = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: mark.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Event\EventJudgeController::mark
* @see app/Http/Controllers/DigitalJudge/Event/EventJudgeController.php:52
* @route '//judge.localhost/v2/{competition}/event/{event}/time/mark/{heat}'
*/
mark.head = (args: { competition: number | { id: number }, event: number | { id: number }, heat: string | number } | [competition: number | { id: number }, event: number | { id: number }, heat: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: mark.url(args, options),
    method: 'head',
})

const time = {
    selectHeat: Object.assign(selectHeat, selectHeat),
    mark: Object.assign(mark, mark2a6e27),
}

export default time