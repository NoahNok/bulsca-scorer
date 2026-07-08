import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\DigitalJudge\JudgeController::loginPost
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:58
* @route '//judge.localhost/new/login'
*/
export const loginPost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: loginPost.url(options),
    method: 'post',
})

loginPost.definition = {
    methods: ["post"],
    url: '//judge.localhost/new/login',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::loginPost
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:58
* @route '//judge.localhost/new/login'
*/
loginPost.url = (options?: RouteQueryOptions) => {
    return loginPost.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::loginPost
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:58
* @route '//judge.localhost/new/login'
*/
loginPost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: loginPost.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::resendPin
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:41
* @route '//judge.localhost/new/login/resend-pin'
*/
export const resendPin = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendPin.url(options),
    method: 'post',
})

resendPin.definition = {
    methods: ["post"],
    url: '//judge.localhost/new/login/resend-pin',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::resendPin
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:41
* @route '//judge.localhost/new/login/resend-pin'
*/
resendPin.url = (options?: RouteQueryOptions) => {
    return resendPin.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::resendPin
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:41
* @route '//judge.localhost/new/login/resend-pin'
*/
resendPin.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resendPin.url(options),
    method: 'post',
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
* @see \App\Http\Controllers\DigitalJudge\JudgeController::home
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:116
* @route '//judge.localhost/new/{competition}'
*/
export const home = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(args, options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::home
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:116
* @route '//judge.localhost/new/{competition}'
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
* @route '//judge.localhost/new/{competition}'
*/
home.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::home
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:116
* @route '//judge.localhost/new/{competition}'
*/
home.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::confirmJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:151
* @route '//judge.localhost/new/{competition}/serc/confirm/{judge}'
*/
export const confirmJudge = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmJudge.url(args, options),
    method: 'get',
})

confirmJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc/confirm/{judge}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::confirmJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:151
* @route '//judge.localhost/new/{competition}/serc/confirm/{judge}'
*/
confirmJudge.url = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            judge: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return confirmJudge.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::confirmJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:151
* @route '//judge.localhost/new/{competition}/serc/confirm/{judge}'
*/
confirmJudge.get = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::confirmJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:151
* @route '//judge.localhost/new/{competition}/serc/confirm/{judge}'
*/
confirmJudge.head = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirmJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::confirmJudgePost
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:171
* @route '//judge.localhost/new/{competition}/serc/confirm'
*/
export const confirmJudgePost = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmJudgePost.url(args, options),
    method: 'post',
})

confirmJudgePost.definition = {
    methods: ["post"],
    url: '//judge.localhost/new/{competition}/serc/confirm',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::confirmJudgePost
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:171
* @route '//judge.localhost/new/{competition}/serc/confirm'
*/
confirmJudgePost.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return confirmJudgePost.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::confirmJudgePost
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:171
* @route '//judge.localhost/new/{competition}/serc/confirm'
*/
confirmJudgePost.post = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmJudgePost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::addJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:255
* @route '//judge.localhost/new/{competition}/serc/add-judge'
*/
export const addJudge = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: addJudge.url(args, options),
    method: 'get',
})

addJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc/add-judge',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::addJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:255
* @route '//judge.localhost/new/{competition}/serc/add-judge'
*/
addJudge.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return addJudge.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::addJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:255
* @route '//judge.localhost/new/{competition}/serc/add-judge'
*/
addJudge.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: addJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::addJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:255
* @route '//judge.localhost/new/{competition}/serc/add-judge'
*/
addJudge.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: addJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::attachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:281
* @route '//judge.localhost/new/{competition}/serc/attach-judge/{judge}'
*/
export const attachJudge = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: attachJudge.url(args, options),
    method: 'get',
})

attachJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc/attach-judge/{judge}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::attachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:281
* @route '//judge.localhost/new/{competition}/serc/attach-judge/{judge}'
*/
attachJudge.url = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            judge: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return attachJudge.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::attachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:281
* @route '//judge.localhost/new/{competition}/serc/attach-judge/{judge}'
*/
attachJudge.get = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: attachJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::attachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:281
* @route '//judge.localhost/new/{competition}/serc/attach-judge/{judge}'
*/
attachJudge.head = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: attachJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::detachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:293
* @route '//judge.localhost/new/{competition}/serc/detach-judge/{judge}'
*/
export const detachJudge = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detachJudge.url(args, options),
    method: 'get',
})

detachJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc/detach-judge/{judge}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::detachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:293
* @route '//judge.localhost/new/{competition}/serc/detach-judge/{judge}'
*/
detachJudge.url = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            judge: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return detachJudge.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::detachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:293
* @route '//judge.localhost/new/{competition}/serc/detach-judge/{judge}'
*/
detachJudge.get = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detachJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::detachJudge
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:293
* @route '//judge.localhost/new/{competition}/serc/detach-judge/{judge}'
*/
detachJudge.head = (args: { competition: number | { id: number }, judge: number | { id: number } } | [competition: number | { id: number }, judge: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detachJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::selectTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:186
* @route '//judge.localhost/new/{competition}/serc/select-tank'
*/
export const selectTank = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectTank.url(args, options),
    method: 'get',
})

selectTank.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc/select-tank',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::selectTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:186
* @route '//judge.localhost/new/{competition}/serc/select-tank'
*/
selectTank.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return selectTank.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::selectTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:186
* @route '//judge.localhost/new/{competition}/serc/select-tank'
*/
selectTank.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectTank.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::selectTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:186
* @route '//judge.localhost/new/{competition}/serc/select-tank'
*/
selectTank.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: selectTank.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::setTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:194
* @route '//judge.localhost/new/{competition}/serc/select-tank/{tank}'
*/
export const setTank = (args: { competition: number | { id: number }, tank: string | number } | [competition: number | { id: number }, tank: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setTank.url(args, options),
    method: 'get',
})

setTank.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc/select-tank/{tank}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::setTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:194
* @route '//judge.localhost/new/{competition}/serc/select-tank/{tank}'
*/
setTank.url = (args: { competition: number | { id: number }, tank: string | number } | [competition: number | { id: number }, tank: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            tank: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        tank: args.tank,
    }

    return setTank.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{tank}', parsedArgs.tank.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::setTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:194
* @route '//judge.localhost/new/{competition}/serc/select-tank/{tank}'
*/
setTank.get = (args: { competition: number | { id: number }, tank: string | number } | [competition: number | { id: number }, tank: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setTank.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::setTank
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:194
* @route '//judge.localhost/new/{competition}/serc/select-tank/{tank}'
*/
setTank.head = (args: { competition: number | { id: number }, tank: string | number } | [competition: number | { id: number }, tank: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: setTank.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::sercHome
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:201
* @route '//judge.localhost/new/{competition}/serc'
*/
export const sercHome = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sercHome.url(args, options),
    method: 'get',
})

sercHome.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/new/{competition}/serc',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::sercHome
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:201
* @route '//judge.localhost/new/{competition}/serc'
*/
sercHome.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return sercHome.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::sercHome
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:201
* @route '//judge.localhost/new/{competition}/serc'
*/
sercHome.get = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sercHome.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\JudgeController::sercHome
* @see app/Http/Controllers/DigitalJudge/JudgeController.php:201
* @route '//judge.localhost/new/{competition}/serc'
*/
sercHome.head = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sercHome.url(args, options),
    method: 'head',
})

const JudgeController = { login, loginPost, resendPin, index, joinCompetition, home, confirmJudge, confirmJudgePost, addJudge, attachJudge, detachJudge, selectTank, setTank, sercHome }

export default JudgeController