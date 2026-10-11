import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import loginDf2c2a from './login'
import register702019 from './register'
import password from './password'
import accountAe48a0 from './account'
import competition633808 from './competition'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::login
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:16
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
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::login
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:16
* @route '//judge.localhost/v2/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::login
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:16
* @route '//judge.localhost/v2/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::login
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:16
* @route '//judge.localhost/v2/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::register
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:17
* @route '//judge.localhost/v2/register'
*/
export const register = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

register.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/register',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::register
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:17
* @route '//judge.localhost/v2/register'
*/
register.url = (options?: RouteQueryOptions) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::register
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:17
* @route '//judge.localhost/v2/register'
*/
register.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\RegisterController::register
* @see app/Http/Controllers/DigitalJudge/Auth/RegisterController.php:17
* @route '//judge.localhost/v2/register'
*/
register.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: register.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:20
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
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:20
* @route '//judge.localhost/v2'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:20
* @route '//judge.localhost/v2'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::index
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:20
* @route '//judge.localhost/v2'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::joinCompetition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:31
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
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:31
* @route '//judge.localhost/v2/join'
*/
joinCompetition.url = (options?: RouteQueryOptions) => {
    return joinCompetition.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::joinCompetition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:31
* @route '//judge.localhost/v2/join'
*/
joinCompetition.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: joinCompetition.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::logout
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:141
* @route '//judge.localhost/v2/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::logout
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:141
* @route '//judge.localhost/v2/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\LoginController::logout
* @see app/Http/Controllers/DigitalJudge/Auth/LoginController.php:141
* @route '//judge.localhost/v2/logout'
*/
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::account
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:17
* @route '//judge.localhost/v2/account'
*/
export const account = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(options),
    method: 'get',
})

account.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/account',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::account
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:17
* @route '//judge.localhost/v2/account'
*/
account.url = (options?: RouteQueryOptions) => {
    return account.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::account
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:17
* @route '//judge.localhost/v2/account'
*/
account.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::account
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:17
* @route '//judge.localhost/v2/account'
*/
account.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: account.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::competition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:45
* @route '//judge.localhost/v2/{competition}'
*/
export const competition = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: competition.url(args, options),
    method: 'get',
})

competition.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::competition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:45
* @route '//judge.localhost/v2/{competition}'
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
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:45
* @route '//judge.localhost/v2/{competition}'
*/
competition.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: competition.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::competition
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:45
* @route '//judge.localhost/v2/{competition}'
*/
competition.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: competition.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::refereeToggle
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:80
* @route '//judge.localhost/v2/switchreferee/{competition}'
*/
export const refereeToggle = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: refereeToggle.url(args, options),
    method: 'get',
})

refereeToggle.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/switchreferee/{competition}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::refereeToggle
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:80
* @route '//judge.localhost/v2/switchreferee/{competition}'
*/
refereeToggle.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return refereeToggle.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::refereeToggle
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:80
* @route '//judge.localhost/v2/switchreferee/{competition}'
*/
refereeToggle.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: refereeToggle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::refereeToggle
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:80
* @route '//judge.localhost/v2/switchreferee/{competition}'
*/
refereeToggle.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: refereeToggle.url(args, options),
    method: 'head',
})

const judge = {
    login: Object.assign(login, loginDf2c2a),
    register: Object.assign(register, register702019),
    password: Object.assign(password, password),
    index: Object.assign(index, index),
    joinCompetition: Object.assign(joinCompetition, joinCompetition),
    logout: Object.assign(logout, logout),
    account: Object.assign(account, accountAe48a0),
    competition: Object.assign(competition, competition633808),
    refereeToggle: Object.assign(refereeToggle, refereeToggle),
}

export default judge