import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::login
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:33
* @route '//judge.localhost/v2/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::login
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:33
* @route '//judge.localhost/v2/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::login
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:33
* @route '//judge.localhost/v2/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::login
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:33
* @route '//judge.localhost/v2/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::loginPost
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:58
* @route '//judge.localhost/v2/login'
*/
export const loginPost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: loginPost.url(options),
    method: 'post',
})

loginPost.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::loginPost
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:58
* @route '//judge.localhost/v2/login'
*/
loginPost.url = (options?: RouteQueryOptions) => {
    return loginPost.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::loginPost
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:58
* @route '//judge.localhost/v2/login'
*/
loginPost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: loginPost.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::resendPin
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:41
* @route '//judge.localhost/v2/login/resend-pin'
*/
export const resendPin = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendPin.url(options),
    method: 'post',
})

resendPin.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/login/resend-pin',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::resendPin
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:41
* @route '//judge.localhost/v2/login/resend-pin'
*/
resendPin.url = (options?: RouteQueryOptions) => {
    return resendPin.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::resendPin
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:41
* @route '//judge.localhost/v2/login/resend-pin'
*/
resendPin.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendPin.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:22
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
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:22
* @route '//judge.localhost/v2'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:22
* @route '//judge.localhost/v2'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:22
* @route '//judge.localhost/v2'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::joinCompetition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:102
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
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:102
* @route '//judge.localhost/v2/join'
*/
joinCompetition.url = (options?: RouteQueryOptions) => {
    return joinCompetition.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::joinCompetition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:102
* @route '//judge.localhost/v2/join'
*/
joinCompetition.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: joinCompetition.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::toggleReferee
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:151
* @route '//judge.localhost/v2/switchreferee'
*/
export const toggleReferee = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: toggleReferee.url(options),
    method: 'get',
})

toggleReferee.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/switchreferee',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::toggleReferee
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:151
* @route '//judge.localhost/v2/switchreferee'
*/
toggleReferee.url = (options?: RouteQueryOptions) => {
    return toggleReferee.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::toggleReferee
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:151
* @route '//judge.localhost/v2/switchreferee'
*/
toggleReferee.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: toggleReferee.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::toggleReferee
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:151
* @route '//judge.localhost/v2/switchreferee'
*/
toggleReferee.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: toggleReferee.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::home
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:116
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
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:116
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
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:116
* @route '//judge.localhost/v2/{competition}'
*/
home.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::home
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:116
* @route '//judge.localhost/v2/{competition}'
*/
home.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(args, options),
    method: 'head',
})

const JudgeController = { login, loginPost, resendPin, index, joinCompetition, toggleReferee, home }

export default JudgeController