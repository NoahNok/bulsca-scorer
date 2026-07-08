import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::index
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:21
* @route '//judge.localhost'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//judge.localhost',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::index
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:21
* @route '//judge.localhost'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::index
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:21
* @route '//judge.localhost'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::index
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:21
* @route '//judge.localhost'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::login
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:27
* @route '//judge.localhost/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

login.definition = {
    methods: ["post"],
    url: '//judge.localhost/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::login
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:27
* @route '//judge.localhost/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::login
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:27
* @route '//judge.localhost/login'
*/
login.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::logout
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:50
* @route '//judge.localhost/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logout.url(options),
    method: 'get',
})

logout.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/logout',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::logout
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:50
* @route '//judge.localhost/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::logout
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:50
* @route '//judge.localhost/logout'
*/
logout.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logout.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::logout
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:50
* @route '//judge.localhost/logout'
*/
logout.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logout.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::home
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:65
* @route '//judge.localhost/home'
*/
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/home',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::home
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:65
* @route '//judge.localhost/home'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::home
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:65
* @route '//judge.localhost/home'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::home
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:65
* @route '//judge.localhost/home'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::help
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:177
* @route '//judge.localhost/help'
*/
export const help = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: help.url(options),
    method: 'get',
})

help.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/help',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::help
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:177
* @route '//judge.localhost/help'
*/
help.url = (options?: RouteQueryOptions) => {
    return help.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::help
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:177
* @route '//judge.localhost/help'
*/
help.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: help.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::help
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:177
* @route '//judge.localhost/help'
*/
help.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: help.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmResults
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:107
* @route '//judge.localhost/confirm/serc/{serc}'
*/
export const confirmResults = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmResults.url(args, options),
    method: 'get',
})

confirmResults.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/confirm/serc/{serc}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmResults
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:107
* @route '//judge.localhost/confirm/serc/{serc}'
*/
confirmResults.url = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { serc: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { serc: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            serc: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return confirmResults.definition.url
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmResults
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:107
* @route '//judge.localhost/confirm/serc/{serc}'
*/
confirmResults.get = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmResults
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:107
* @route '//judge.localhost/confirm/serc/{serc}'
*/
confirmResults.head = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirmResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmResultsPost
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:113
* @route '//judge.localhost/confirm/serc/{serc}'
*/
export const confirmResultsPost = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmResultsPost.url(args, options),
    method: 'post',
})

confirmResultsPost.definition = {
    methods: ["post"],
    url: '//judge.localhost/confirm/serc/{serc}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmResultsPost
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:113
* @route '//judge.localhost/confirm/serc/{serc}'
*/
confirmResultsPost.url = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { serc: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { serc: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            serc: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return confirmResultsPost.definition.url
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmResultsPost
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:113
* @route '//judge.localhost/confirm/serc/{serc}'
*/
confirmResultsPost.post = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmResultsPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmSpeedResults
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:123
* @route '//judge.localhost/confirm/speed/{speed}'
*/
export const confirmSpeedResults = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmSpeedResults.url(args, options),
    method: 'get',
})

confirmSpeedResults.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/confirm/speed/{speed}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmSpeedResults
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:123
* @route '//judge.localhost/confirm/speed/{speed}'
*/
confirmSpeedResults.url = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { speed: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { speed: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            speed: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        speed: typeof args.speed === 'object'
        ? args.speed.id
        : args.speed,
    }

    return confirmSpeedResults.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmSpeedResults
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:123
* @route '//judge.localhost/confirm/speed/{speed}'
*/
confirmSpeedResults.get = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmSpeedResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmSpeedResults
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:123
* @route '//judge.localhost/confirm/speed/{speed}'
*/
confirmSpeedResults.head = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirmSpeedResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmSpeedResultsPost
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:129
* @route '//judge.localhost/confirm/speed/{speed}'
*/
export const confirmSpeedResultsPost = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmSpeedResultsPost.url(args, options),
    method: 'post',
})

confirmSpeedResultsPost.definition = {
    methods: ["post"],
    url: '//judge.localhost/confirm/speed/{speed}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmSpeedResultsPost
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:129
* @route '//judge.localhost/confirm/speed/{speed}'
*/
confirmSpeedResultsPost.url = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { speed: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { speed: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            speed: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        speed: typeof args.speed === 'object'
        ? args.speed.id
        : args.speed,
    }

    return confirmSpeedResultsPost.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmSpeedResultsPost
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:129
* @route '//judge.localhost/confirm/speed/{speed}'
*/
confirmSpeedResultsPost.post = (args: { speed: number | { id: number } } | [speed: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmSpeedResultsPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::toggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:71
* @route '/comps/{comp}/digital-judge-toggle'
*/
export const toggle = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: toggle.url(args, options),
    method: 'get',
})

toggle.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/digital-judge-toggle',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::toggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:71
* @route '/comps/{comp}/digital-judge-toggle'
*/
toggle.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { comp: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
    }

    return toggle.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::toggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:71
* @route '/comps/{comp}/digital-judge-toggle'
*/
toggle.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: toggle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::toggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:71
* @route '/comps/{comp}/digital-judge-toggle'
*/
toggle.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: toggle.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::settingsPost
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:183
* @route '/comps/{comp}/digital-judge-settings'
*/
export const settingsPost = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: settingsPost.url(args, options),
    method: 'post',
})

settingsPost.definition = {
    methods: ["post"],
    url: '/comps/{comp}/digital-judge-settings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::settingsPost
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:183
* @route '/comps/{comp}/digital-judge-settings'
*/
settingsPost.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { comp: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
    }

    return settingsPost.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::settingsPost
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:183
* @route '/comps/{comp}/digital-judge-settings'
*/
settingsPost.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: settingsPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::qrs
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:204
* @route '/comps/{comp}/digital-judge-qrs'
*/
export const qrs = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: qrs.url(args, options),
    method: 'get',
})

qrs.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/digital-judge-qrs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::qrs
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:204
* @route '/comps/{comp}/digital-judge-qrs'
*/
qrs.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { comp: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
    }

    return qrs.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::qrs
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:204
* @route '/comps/{comp}/digital-judge-qrs'
*/
qrs.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: qrs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::qrs
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:204
* @route '/comps/{comp}/digital-judge-qrs'
*/
qrs.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: qrs.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::speedToggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:99
* @route '/comps/{comp}/events/speeds/{event}/digital-judge-toggle'
*/
export const speedToggle = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: speedToggle.url(args, options),
    method: 'get',
})

speedToggle.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/speeds/{event}/digital-judge-toggle',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::speedToggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:99
* @route '/comps/{comp}/events/speeds/{event}/digital-judge-toggle'
*/
speedToggle.url = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            event: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
    }

    return speedToggle.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::speedToggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:99
* @route '/comps/{comp}/events/speeds/{event}/digital-judge-toggle'
*/
speedToggle.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: speedToggle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::speedToggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:99
* @route '/comps/{comp}/events/speeds/{event}/digital-judge-toggle'
*/
speedToggle.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: speedToggle.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::sercToggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:91
* @route '/comps/{comp}/events/sercs/{serc}/digital-judge-toggle'
*/
export const sercToggle = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sercToggle.url(args, options),
    method: 'get',
})

sercToggle.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/digital-judge-toggle',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::sercToggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:91
* @route '/comps/{comp}/events/sercs/{serc}/digital-judge-toggle'
*/
sercToggle.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return sercToggle.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::sercToggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:91
* @route '/comps/{comp}/events/sercs/{serc}/digital-judge-toggle'
*/
sercToggle.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sercToggle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::sercToggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:91
* @route '/comps/{comp}/events/sercs/{serc}/digital-judge-toggle'
*/
sercToggle.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sercToggle.url(args, options),
    method: 'head',
})

const DigitalJudgeController = { index, login, logout, home, help, confirmResults, confirmResultsPost, confirmSpeedResults, confirmSpeedResultsPost, toggle, settingsPost, qrs, speedToggle, sercToggle }

export default DigitalJudgeController