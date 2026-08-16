import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import tankCe0591 from './tank'
import addJudge007e41 from './add-judge'
import removeJudge96e709 from './remove-judge'
import tutorial291b9f from './tutorial'
import overallCommentsCd6839 from './overall-comments'
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
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::confirm
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:44
* @route '//judge.localhost/judging/{judge}/confirm-judge'
*/
export const confirm = (args: { judge: number | { id: number } } | [judge: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirm.url(args, options),
    method: 'post',
})

confirm.definition = {
    methods: ["post"],
    url: '//judge.localhost/judging/{judge}/confirm-judge',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::confirm
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:44
* @route '//judge.localhost/judging/{judge}/confirm-judge'
*/
confirm.url = (args: { judge: number | { id: number } } | [judge: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return confirm.definition.url
            .replace('{judge}', parsedArgs.judge.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::confirm
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:44
* @route '//judge.localhost/judging/{judge}/confirm-judge'
*/
confirm.post = (args: { judge: number | { id: number } } | [judge: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: confirm.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tank
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:73
* @route '//judge.localhost/judging/tank'
*/
export const tank = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tank.url(options),
    method: 'get',
})

tank.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/tank',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tank
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:73
* @route '//judge.localhost/judging/tank'
*/
tank.url = (options?: RouteQueryOptions) => {
    return tank.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tank
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:73
* @route '//judge.localhost/judging/tank'
*/
tank.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tank.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tank
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:73
* @route '//judge.localhost/judging/tank'
*/
tank.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: tank.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::home
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:56
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
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:56
* @route '//judge.localhost/judging/home'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::home
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:56
* @route '//judge.localhost/judging/home'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::home
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:56
* @route '//judge.localhost/judging/home'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::addJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:254
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
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:254
* @route '//judge.localhost/judging/add-judge'
*/
addJudge.url = (options?: RouteQueryOptions) => {
    return addJudge.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::addJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:254
* @route '//judge.localhost/judging/add-judge'
*/
addJudge.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: addJudge.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::addJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:254
* @route '//judge.localhost/judging/add-judge'
*/
addJudge.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: addJudge.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::removeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:268
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
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:268
* @route '//judge.localhost/judging/remove-judge'
*/
removeJudge.url = (options?: RouteQueryOptions) => {
    return removeJudge.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::removeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:268
* @route '//judge.localhost/judging/remove-judge'
*/
removeJudge.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: removeJudge.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::removeJudge
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:268
* @route '//judge.localhost/judging/remove-judge'
*/
removeJudge.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: removeJudge.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::nextTeam
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:101
* @route '//judge.localhost/judging/team/next'
*/
export const nextTeam = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: nextTeam.url(options),
    method: 'get',
})

nextTeam.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/judging/team/next',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::nextTeam
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:101
* @route '//judge.localhost/judging/team/next'
*/
nextTeam.url = (options?: RouteQueryOptions) => {
    return nextTeam.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::nextTeam
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:101
* @route '//judge.localhost/judging/team/next'
*/
nextTeam.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: nextTeam.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::nextTeam
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:101
* @route '//judge.localhost/judging/team/next'
*/
nextTeam.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: nextTeam.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::judgeTeam
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:140
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
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:140
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
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:140
* @route '//judge.localhost/judging/team/{entity_id}'
*/
judgeTeam.get = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: judgeTeam.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::judgeTeam
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:140
* @route '//judge.localhost/judging/team/{entity_id}'
*/
judgeTeam.head = (args: { entity_id: string | number } | [entity_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: judgeTeam.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::saveTeamScores
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:165
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
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:165
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
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:165
* @route '//judge.localhost/judging/team/{team}'
*/
saveTeamScores.post = (args: { team: string | number } | [team: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveTeamScores.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tutorial
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:321
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
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:321
* @route '//judge.localhost/judging/tutorial'
*/
tutorial.url = (options?: RouteQueryOptions) => {
    return tutorial.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tutorial
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:321
* @route '//judge.localhost/judging/tutorial'
*/
tutorial.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tutorial.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::tutorial
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:321
* @route '//judge.localhost/judging/tutorial'
*/
tutorial.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: tutorial.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::previousMarks
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:282
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
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:282
* @route '//judge.localhost/judging/previous-marks'
*/
previousMarks.url = (options?: RouteQueryOptions) => {
    return previousMarks.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::previousMarks
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:282
* @route '//judge.localhost/judging/previous-marks'
*/
previousMarks.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: previousMarks.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::previousMarks
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:282
* @route '//judge.localhost/judging/previous-marks'
*/
previousMarks.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: previousMarks.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::overallComments
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:349
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
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:349
* @route '//judge.localhost/judging/overall-comments'
*/
overallComments.url = (options?: RouteQueryOptions) => {
    return overallComments.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::overallComments
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:349
* @route '//judge.localhost/judging/overall-comments'
*/
overallComments.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: overallComments.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::overallComments
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:349
* @route '//judge.localhost/judging/overall-comments'
*/
overallComments.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: overallComments.url(options),
    method: 'head',
})

const judging = {
    confirmJudge: Object.assign(confirmJudge, confirmJudge),
    confirm: Object.assign(confirm, confirm),
    tank: Object.assign(tank, tankCe0591),
    home: Object.assign(home, home),
    addJudge: Object.assign(addJudge, addJudge007e41),
    removeJudge: Object.assign(removeJudge, removeJudge96e709),
    nextTeam: Object.assign(nextTeam, nextTeam),
    judgeTeam: Object.assign(judgeTeam, judgeTeam),
    saveTeamScores: Object.assign(saveTeamScores, saveTeamScores),
    tutorial: Object.assign(tutorial, tutorial291b9f),
    previousMarks: Object.assign(previousMarks, previousMarks),
    overallComments: Object.assign(overallComments, overallCommentsCd6839),
}

export default judging