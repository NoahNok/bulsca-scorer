import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import loginDf2c2a from './login'
import competition633808 from './competition'
/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::login
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:33
* @route '//judge.localhost/new/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::login
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:33
* @route '//judge.localhost/new/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::login
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:33
* @route '//judge.localhost/new/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::login
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:33
* @route '//judge.localhost/new/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:22
* @route '//judge.localhost/new'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:22
* @route '//judge.localhost/new'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:22
* @route '//judge.localhost/new'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:22
* @route '//judge.localhost/new'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::joinCompetition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:102
* @route '//judge.localhost/new/join'
*/
export const joinCompetition = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: joinCompetition.url(options),
    method: 'post',
})

joinCompetition.definition = {
    methods: ["post"],
    url: '//judge.localhost/new/join',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::joinCompetition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:102
* @route '//judge.localhost/new/join'
*/
joinCompetition.url = (options?: RouteQueryOptions) => {
    return joinCompetition.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::joinCompetition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:102
* @route '//judge.localhost/new/join'
*/
joinCompetition.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: joinCompetition.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::competition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:116
* @route '//judge.localhost/new/{competition}'
*/
export const competition = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: competition.url(args, options),
    method: 'get',
})

competition.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::competition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:116
* @route '//judge.localhost/new/{competition}'
*/
competition.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return competition.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::competition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:116
* @route '//judge.localhost/new/{competition}'
*/
competition.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: competition.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::competition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:116
* @route '//judge.localhost/new/{competition}'
*/
competition.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: competition.url(args, options),
    method: 'head',
})

const judge = {
    login: Object.assign(login, loginDf2c2a),
    index: Object.assign(index, index),
    joinCompetition: Object.assign(joinCompetition, joinCompetition),
    competition: Object.assign(competition, competition633808),
}

export default judge