import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
import confirmResultsEf1296 from './confirm-results'
import time from './time'
import oof from './oof'
/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::confirmResults
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:68
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
export const confirmResults = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmResults.url(args, options),
    method: 'get',
})

confirmResults.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/event/{event}/confirm-results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::confirmResults
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:68
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
confirmResults.url = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return confirmResults.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::confirmResults
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:68
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
confirmResults.get = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\ConfirmResultsController::confirmResults
* @see app/Http/Controllers/DigitalJudge/ConfirmResultsController.php:68
* @route '//judge.localhost/v2/{competition}/event/{event}/confirm-results'
*/
confirmResults.head = (args: { competition: number | { id: number }, event: number | { id: number } } | [competition: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirmResults.url(args, options),
    method: 'head',
})

const event = {
    confirmResults: Object.assign(confirmResults, confirmResultsEf1296),
    time: Object.assign(time, time),
    oof: Object.assign(oof, oof),
}

export default event