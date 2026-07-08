import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::confirmJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:27
* @route '//judge.localhost/judging/{judge}/confirm-judge'
*/
export const confirmJudge = (args: { judge: number | { id: number } } | [judge: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmJudge.url(args, options),
    method: 'get',
})

confirmJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/{judge}/confirm-judge',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::confirmJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:27
* @route '//judge.localhost/judging/{judge}/confirm-judge'
*/
confirmJudge.url = (args: { judge: number | { id: number } } | [judge: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { judge: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { judge: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            judge: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return confirmJudge.definition.url
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::confirmJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:27
* @route '//judge.localhost/judging/{judge}/confirm-judge'
*/
confirmJudge.get = (args: { judge: number | { id: number } } | [judge: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirmJudge.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::confirmJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:27
* @route '//judge.localhost/judging/{judge}/confirm-judge'
*/
confirmJudge.head = (args: { judge: number | { id: number } } | [judge: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirmJudge.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::confirmJudgePost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:42
* @route '//judge.localhost/judging/{judge}/confirm-judge'
*/
export const confirmJudgePost = (args: { judge: number | { id: number } } | [judge: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmJudgePost.url(args, options),
    method: 'post',
})

confirmJudgePost.definition = {
    methods: ["post"],
    url: '//judge.localhost/judging/{judge}/confirm-judge',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::confirmJudgePost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:42
* @route '//judge.localhost/judging/{judge}/confirm-judge'
*/
confirmJudgePost.url = (args: { judge: number | { id: number } } | [judge: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { judge: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { judge: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            judge: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        judge: typeof args.judge === 'object'
        ? args.judge.id
        : args.judge,
    }

    return confirmJudgePost.definition.url
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::confirmJudgePost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:42
* @route '//judge.localhost/judging/{judge}/confirm-judge'
*/
confirmJudgePost.post = (args: { judge: number | { id: number } } | [judge: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirmJudgePost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::selectTank
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:66
* @route '//judge.localhost/judging/tank'
*/
export const selectTank = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectTank.url(options),
    method: 'get',
})

selectTank.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/tank',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::selectTank
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:66
* @route '//judge.localhost/judging/tank'
*/
selectTank.url = (options?: RouteQueryOptions) => {
    return selectTank.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::selectTank
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:66
* @route '//judge.localhost/judging/tank'
*/
selectTank.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: selectTank.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::selectTank
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:66
* @route '//judge.localhost/judging/tank'
*/
selectTank.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: selectTank.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::setTank
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:77
* @route '//judge.localhost/judging/tank/{tank}'
*/
export const setTank = (args: { tank: string | number } | [tank: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setTank.url(args, options),
    method: 'get',
})

setTank.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/tank/{tank}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::setTank
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:77
* @route '//judge.localhost/judging/tank/{tank}'
*/
setTank.url = (args: { tank: string | number } | [tank: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { tank: args }
    }

    if (Array.isArray(args)) {
        args = {
            tank: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        tank: args.tank,
    }

    return setTank.definition.url
            .replace('{tank}', parsedArgs.tank.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::setTank
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:77
* @route '//judge.localhost/judging/tank/{tank}'
*/
setTank.get = (args: { tank: string | number } | [tank: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setTank.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::setTank
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:77
* @route '//judge.localhost/judging/tank/{tank}'
*/
setTank.head = (args: { tank: string | number } | [tank: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: setTank.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::home
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:54
* @route '//judge.localhost/judging/home'
*/
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/home',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::home
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:54
* @route '//judge.localhost/judging/home'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::home
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:54
* @route '//judge.localhost/judging/home'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::home
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:54
* @route '//judge.localhost/judging/home'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::addJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:240
* @route '//judge.localhost/judging/add-judge'
*/
export const addJudge = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: addJudge.url(options),
    method: 'get',
})

addJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/add-judge',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::addJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:240
* @route '//judge.localhost/judging/add-judge'
*/
addJudge.url = (options?: RouteQueryOptions) => {
    return addJudge.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::addJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:240
* @route '//judge.localhost/judging/add-judge'
*/
addJudge.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: addJudge.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::addJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:240
* @route '//judge.localhost/judging/add-judge'
*/
addJudge.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: addJudge.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::addJudgePost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:245
* @route '//judge.localhost/judging/add-judge'
*/
export const addJudgePost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addJudgePost.url(options),
    method: 'post',
})

addJudgePost.definition = {
    methods: ["post"],
    url: '//judge.localhost/judging/add-judge',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::addJudgePost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:245
* @route '//judge.localhost/judging/add-judge'
*/
addJudgePost.url = (options?: RouteQueryOptions) => {
    return addJudgePost.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::addJudgePost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:245
* @route '//judge.localhost/judging/add-judge'
*/
addJudgePost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addJudgePost.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::removeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:254
* @route '//judge.localhost/judging/remove-judge'
*/
export const removeJudge = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: removeJudge.url(options),
    method: 'get',
})

removeJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/remove-judge',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::removeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:254
* @route '//judge.localhost/judging/remove-judge'
*/
removeJudge.url = (options?: RouteQueryOptions) => {
    return removeJudge.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::removeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:254
* @route '//judge.localhost/judging/remove-judge'
*/
removeJudge.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: removeJudge.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::removeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:254
* @route '//judge.localhost/judging/remove-judge'
*/
removeJudge.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: removeJudge.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::removeJudgePost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:259
* @route '//judge.localhost/judging/remove-judge'
*/
export const removeJudgePost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeJudgePost.url(options),
    method: 'post',
})

removeJudgePost.definition = {
    methods: ["post"],
    url: '//judge.localhost/judging/remove-judge',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::removeJudgePost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:259
* @route '//judge.localhost/judging/remove-judge'
*/
removeJudgePost.url = (options?: RouteQueryOptions) => {
    return removeJudgePost.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::removeJudgePost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:259
* @route '//judge.localhost/judging/remove-judge'
*/
removeJudgePost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeJudgePost.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::nextTeamForJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:94
* @route '//judge.localhost/judging/team/next'
*/
export const nextTeamForJudge = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: nextTeamForJudge.url(options),
    method: 'get',
})

nextTeamForJudge.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/team/next',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::nextTeamForJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:94
* @route '//judge.localhost/judging/team/next'
*/
nextTeamForJudge.url = (options?: RouteQueryOptions) => {
    return nextTeamForJudge.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::nextTeamForJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:94
* @route '//judge.localhost/judging/team/next'
*/
nextTeamForJudge.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: nextTeamForJudge.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::nextTeamForJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:94
* @route '//judge.localhost/judging/team/next'
*/
nextTeamForJudge.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: nextTeamForJudge.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::judgeTeam
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:133
* @route '//judge.localhost/judging/team/{entity_id}'
*/
export const judgeTeam = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: judgeTeam.url(args, options),
    method: 'get',
})

judgeTeam.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/team/{entity_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::judgeTeam
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:133
* @route '//judge.localhost/judging/team/{entity_id}'
*/
judgeTeam.url = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { entity_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            entity_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        entity_id: args.entity_id,
    }

    return judgeTeam.definition.url
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::judgeTeam
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:133
* @route '//judge.localhost/judging/team/{entity_id}'
*/
judgeTeam.get = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: judgeTeam.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::judgeTeam
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:133
* @route '//judge.localhost/judging/team/{entity_id}'
*/
judgeTeam.head = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: judgeTeam.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::saveTeamScores
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:151
* @route '//judge.localhost/judging/team/{team}'
*/
export const saveTeamScores = (args: { team: string | number } | [team: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveTeamScores.url(args, options),
    method: 'post',
})

saveTeamScores.definition = {
    methods: ["post"],
    url: '//judge.localhost/judging/team/{team}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::saveTeamScores
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:151
* @route '//judge.localhost/judging/team/{team}'
*/
saveTeamScores.url = (args: { team: string | number } | [team: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { team: args }
    }

    if (Array.isArray(args)) {
        args = {
            team: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        team: args.team,
    }

    return saveTeamScores.definition.url
            .replace('{team}', parsedArgs.team.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::saveTeamScores
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:151
* @route '//judge.localhost/judging/team/{team}'
*/
saveTeamScores.post = (args: { team: string | number } | [team: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveTeamScores.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tutorial
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:307
* @route '//judge.localhost/judging/tutorial'
*/
export const tutorial = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tutorial.url(options),
    method: 'get',
})

tutorial.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/tutorial',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tutorial
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:307
* @route '//judge.localhost/judging/tutorial'
*/
tutorial.url = (options?: RouteQueryOptions) => {
    return tutorial.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tutorial
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:307
* @route '//judge.localhost/judging/tutorial'
*/
tutorial.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tutorial.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tutorial
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:307
* @route '//judge.localhost/judging/tutorial'
*/
tutorial.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: tutorial.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tutorialPost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:312
* @route '//judge.localhost/judging/tutorial'
*/
export const tutorialPost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: tutorialPost.url(options),
    method: 'post',
})

tutorialPost.definition = {
    methods: ["post"],
    url: '//judge.localhost/judging/tutorial',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tutorialPost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:312
* @route '//judge.localhost/judging/tutorial'
*/
tutorialPost.url = (options?: RouteQueryOptions) => {
    return tutorialPost.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tutorialPost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:312
* @route '//judge.localhost/judging/tutorial'
*/
tutorialPost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: tutorialPost.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::previousMarks
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:268
* @route '//judge.localhost/judging/previous-marks'
*/
export const previousMarks = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: previousMarks.url(options),
    method: 'get',
})

previousMarks.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/previous-marks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::previousMarks
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:268
* @route '//judge.localhost/judging/previous-marks'
*/
previousMarks.url = (options?: RouteQueryOptions) => {
    return previousMarks.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::previousMarks
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:268
* @route '//judge.localhost/judging/previous-marks'
*/
previousMarks.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: previousMarks.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::previousMarks
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:268
* @route '//judge.localhost/judging/previous-marks'
*/
previousMarks.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: previousMarks.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::overallComments
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:333
* @route '//judge.localhost/judging/overall-comments'
*/
export const overallComments = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overallComments.url(options),
    method: 'get',
})

overallComments.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/overall-comments',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::overallComments
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:333
* @route '//judge.localhost/judging/overall-comments'
*/
overallComments.url = (options?: RouteQueryOptions) => {
    return overallComments.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::overallComments
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:333
* @route '//judge.localhost/judging/overall-comments'
*/
overallComments.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overallComments.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::overallComments
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:333
* @route '//judge.localhost/judging/overall-comments'
*/
overallComments.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: overallComments.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::overallCommentsPost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:338
* @route '//judge.localhost/judging/overall-comments'
*/
export const overallCommentsPost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: overallCommentsPost.url(options),
    method: 'post',
})

overallCommentsPost.definition = {
    methods: ["post"],
    url: '//judge.localhost/judging/overall-comments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::overallCommentsPost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:338
* @route '//judge.localhost/judging/overall-comments'
*/
overallCommentsPost.url = (options?: RouteQueryOptions) => {
    return overallCommentsPost.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::overallCommentsPost
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:338
* @route '//judge.localhost/judging/overall-comments'
*/
overallCommentsPost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: overallCommentsPost.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::changeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:59
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
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:59
* @route '//judge.localhost/change-judge'
*/
changeJudge.url = (options?: RouteQueryOptions) => {
    return changeJudge.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::changeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:59
* @route '//judge.localhost/change-judge'
*/
changeJudge.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: changeJudge.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::changeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:59
* @route '//judge.localhost/change-judge'
*/
changeJudge.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: changeJudge.url(options),
    method: 'head',
})

const DJJudgingController = { confirmJudge, confirmJudgePost, selectTank, setTank, home, addJudge, addJudgePost, removeJudge, removeJudgePost, nextTeamForJudge, judgeTeam, saveTeamScores, tutorial, tutorialPost, previousMarks, overallComments, overallCommentsPost, changeJudge }

export default DJJudgingController