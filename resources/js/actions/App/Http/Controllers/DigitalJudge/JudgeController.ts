import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:21
* @route '//judge.localhost/v2'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:21
* @route '//judge.localhost/v2'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:21
* @route '//judge.localhost/v2'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:21
* @route '//judge.localhost/v2'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::joinCompetition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:32
* @route '//judge.localhost/v2/join'
*/
export const joinCompetition = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: joinCompetition.url(options),
    method: 'post',
})

joinCompetition.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/join',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::joinCompetition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:32
* @route '//judge.localhost/v2/join'
*/
joinCompetition.url = (options?: RouteQueryOptions) => {
    return joinCompetition.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::joinCompetition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:32
* @route '//judge.localhost/v2/join'
*/
joinCompetition.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: joinCompetition.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::home
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:58
* @route '//judge.localhost/v2/{competition}'
*/
export const home = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(args, options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::home
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:58
* @route '//judge.localhost/v2/{competition}'
*/
home.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return home.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::home
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:58
* @route '//judge.localhost/v2/{competition}'
*/
home.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::home
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:58
* @route '//judge.localhost/v2/{competition}'
*/
home.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::officials
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:49
* @route '//judge.localhost/v2/{competition}/officials'
*/
export const officials = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: officials.url(args, options),
    method: 'get',
})

officials.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/officials',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::officials
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:49
* @route '//judge.localhost/v2/{competition}/officials'
*/
officials.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return officials.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::officials
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:49
* @route '//judge.localhost/v2/{competition}/officials'
*/
officials.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: officials.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::officials
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:49
* @route '//judge.localhost/v2/{competition}/officials'
*/
officials.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: officials.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::setSercsReleased
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:106
* @route '//judge.localhost/v2/{competition}/sercs/release'
*/
export const setSercsReleased = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: setSercsReleased.url(args, options),
    method: 'post',
})

setSercsReleased.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/sercs/release',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::setSercsReleased
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:106
* @route '//judge.localhost/v2/{competition}/sercs/release'
*/
setSercsReleased.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return setSercsReleased.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::setSercsReleased
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:106
* @route '//judge.localhost/v2/{competition}/sercs/release'
*/
setSercsReleased.post = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: setSercsReleased.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::toggleReferee
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:117
* @route '//judge.localhost/v2/switchreferee/{competition}'
*/
export const toggleReferee = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: toggleReferee.url(args, options),
    method: 'get',
})

toggleReferee.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/switchreferee/{competition}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::toggleReferee
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:117
* @route '//judge.localhost/v2/switchreferee/{competition}'
*/
toggleReferee.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return toggleReferee.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::toggleReferee
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:117
* @route '//judge.localhost/v2/switchreferee/{competition}'
*/
toggleReferee.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: toggleReferee.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::toggleReferee
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:117
* @route '//judge.localhost/v2/switchreferee/{competition}'
*/
toggleReferee.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: toggleReferee.url(args, options),
    method: 'head',
})

const JudgeController = { index, joinCompetition, home, officials, setSercsReleased, toggleReferee }

export default JudgeController