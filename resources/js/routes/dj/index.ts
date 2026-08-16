import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import judging from './judging'
import speeds from './speeds'
import dq from './dq'
import manage from './manage'
import confirmResultsEf1296 from './confirm-results'
/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::index
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:23
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:23
* @route '//judge.localhost'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::index
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:23
* @route '//judge.localhost'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::index
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:23
* @route '//judge.localhost'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::login
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:29
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:29
* @route '//judge.localhost/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::login
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:29
* @route '//judge.localhost/login'
*/
login.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: login.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::logout
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:52
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:52
* @route '//judge.localhost/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::logout
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:52
* @route '//judge.localhost/logout'
*/
logout.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logout.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::logout
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:52
* @route '//judge.localhost/logout'
*/
logout.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logout.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::home
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:67
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:67
* @route '//judge.localhost/home'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::home
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:67
* @route '//judge.localhost/home'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::home
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:67
* @route '//judge.localhost/home'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::help
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:182
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:182
* @route '//judge.localhost/help'
*/
help.url = (options?: RouteQueryOptions) => {
    return help.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::help
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:182
* @route '//judge.localhost/help'
*/
help.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: help.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::help
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:182
* @route '//judge.localhost/help'
*/
help.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: help.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::changeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:66
* @route '//judge.localhost/change-judge'
*/
export const changeJudge = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: changeJudge.url(options),
    method: 'get',
})

changeJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/change-judge',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::changeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:66
* @route '//judge.localhost/change-judge'
*/
changeJudge.url = (options?: RouteQueryOptions) => {
    return changeJudge.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::changeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:66
* @route '//judge.localhost/change-judge'
*/
changeJudge.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: changeJudge.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::changeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:66
* @route '//judge.localhost/change-judge'
*/
changeJudge.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: changeJudge.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmResults
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:112
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:112
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:112
* @route '//judge.localhost/confirm/serc/{serc}'
*/
confirmResults.get = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::confirmResults
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:112
* @route '//judge.localhost/confirm/serc/{serc}'
*/
confirmResults.head = (args: { serc: number | { id: number } } | [serc: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirmResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::live
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:214
* @route '//judge.localhost/live'
*/
export const live = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: live.url(options),
    method: 'get',
})

live.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/live',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::live
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:214
* @route '//judge.localhost/live'
*/
live.url = (options?: RouteQueryOptions) => {
    return live.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::live
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:214
* @route '//judge.localhost/live'
*/
live.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: live.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::live
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:214
* @route '//judge.localhost/live'
*/
live.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: live.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::toggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:76
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:76
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:76
* @route '/comps/{comp}/digital-judge-toggle'
*/
toggle.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: toggle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::toggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:76
* @route '/comps/{comp}/digital-judge-toggle'
*/
toggle.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: toggle.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::settings
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:188
* @route '/comps/{comp}/digital-judge-settings'
*/
export const settings = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: settings.url(args, options),
    method: 'post',
})

settings.definition = {
    methods: ["post"],
    url: '/comps/{comp}/digital-judge-settings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::settings
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:188
* @route '/comps/{comp}/digital-judge-settings'
*/
settings.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return settings.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::settings
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:188
* @route '/comps/{comp}/digital-judge-settings'
*/
settings.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: settings.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::qrs
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:209
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:209
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:209
* @route '/comps/{comp}/digital-judge-qrs'
*/
qrs.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: qrs.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::qrs
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:209
* @route '/comps/{comp}/digital-judge-qrs'
*/
qrs.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: qrs.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::speedToggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:104
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:104
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:104
* @route '/comps/{comp}/events/speeds/{event}/digital-judge-toggle'
*/
speedToggle.get = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: speedToggle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::speedToggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:104
* @route '/comps/{comp}/events/speeds/{event}/digital-judge-toggle'
*/
speedToggle.head = (args: { comp: number | { id: number }, event: number | { id: number } } | [comp: number | { id: number }, event: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: speedToggle.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::sercToggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:96
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:96
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
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:96
* @route '/comps/{comp}/events/sercs/{serc}/digital-judge-toggle'
*/
sercToggle.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sercToggle.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DigitalJudgeController::sercToggle
* @see app/Http/Controllers/DigitalJudge/DigitalJudgeController.php:96
* @route '/comps/{comp}/events/sercs/{serc}/digital-judge-toggle'
*/
sercToggle.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sercToggle.url(args, options),
    method: 'head',
})

const dj = {
    index: Object.assign(index, index),
    login: Object.assign(login, login),
    logout: Object.assign(logout, logout),
    home: Object.assign(home, home),
    help: Object.assign(help, help),
    judging: Object.assign(judging, judging),
    changeJudge: Object.assign(changeJudge, changeJudge),
    speeds: Object.assign(speeds, speeds),
    dq: Object.assign(dq, dq),
    manage: Object.assign(manage, manage),
    confirmResults: Object.assign(confirmResults, confirmResultsEf1296),
    live: Object.assign(live, live),
    toggle: Object.assign(toggle, toggle),
    settings: Object.assign(settings, settings),
    qrs: Object.assign(qrs, qrs),
    speedToggle: Object.assign(speedToggle, speedToggle),
    sercToggle: Object.assign(sercToggle, sercToggle),
}

export default dj