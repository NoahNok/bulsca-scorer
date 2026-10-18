import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CompetitionController::create
* @see app/Http/Controllers/CompetitionController.php:271
* @route '/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionController::create
* @see app/Http/Controllers/CompetitionController.php:271
* @route '/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::create
* @see app/Http/Controllers/CompetitionController.php:271
* @route '/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionController::create
* @see app/Http/Controllers/CompetitionController.php:271
* @route '/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetitionController::createPost
* @see app/Http/Controllers/CompetitionController.php:276
* @route '/create'
*/
export const createPost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createPost.url(options),
    method: 'post',
})

createPost.definition = {
    methods: ["post"],
    url: '/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::createPost
* @see app/Http/Controllers/CompetitionController.php:276
* @route '/create'
*/
createPost.url = (options?: RouteQueryOptions) => {
    return createPost.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::createPost
* @see app/Http/Controllers/CompetitionController.php:276
* @route '/create'
*/
createPost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createPost.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionController::view
* @see app/Http/Controllers/CompetitionController.php:36
* @route '/comps/{comp}'
*/
export const view = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionController::view
* @see app/Http/Controllers/CompetitionController.php:36
* @route '/comps/{comp}'
*/
view.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return view.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::view
* @see app/Http/Controllers/CompetitionController.php:36
* @route '/comps/{comp}'
*/
view.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionController::view
* @see app/Http/Controllers/CompetitionController.php:36
* @route '/comps/{comp}'
*/
view.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetitionController::activityLog
* @see app/Http/Controllers/CompetitionController.php:65
* @route '/comps/{comp}/activity-log'
*/
export const activityLog = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activityLog.url(args, options),
    method: 'get',
})

activityLog.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/activity-log',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionController::activityLog
* @see app/Http/Controllers/CompetitionController.php:65
* @route '/comps/{comp}/activity-log'
*/
activityLog.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return activityLog.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::activityLog
* @see app/Http/Controllers/CompetitionController.php:65
* @route '/comps/{comp}/activity-log'
*/
activityLog.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: activityLog.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionController::activityLog
* @see app/Http/Controllers/CompetitionController.php:65
* @route '/comps/{comp}/activity-log'
*/
activityLog.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: activityLog.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetitionController::createCompetitionStats
* @see app/Http/Controllers/CompetitionController.php:71
* @route '/comps/{comp}/create-stats'
*/
export const createCompetitionStats = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createCompetitionStats.url(args, options),
    method: 'get',
})

createCompetitionStats.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/create-stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionController::createCompetitionStats
* @see app/Http/Controllers/CompetitionController.php:71
* @route '/comps/{comp}/create-stats'
*/
createCompetitionStats.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return createCompetitionStats.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::createCompetitionStats
* @see app/Http/Controllers/CompetitionController.php:71
* @route '/comps/{comp}/create-stats'
*/
createCompetitionStats.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createCompetitionStats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionController::createCompetitionStats
* @see app/Http/Controllers/CompetitionController.php:71
* @route '/comps/{comp}/create-stats'
*/
createCompetitionStats.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: createCompetitionStats.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetitionController::updateCompetitionSettings
* @see app/Http/Controllers/CompetitionController.php:83
* @route '/comps/{comp}/settings'
*/
export const updateCompetitionSettings = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateCompetitionSettings.url(args, options),
    method: 'post',
})

updateCompetitionSettings.definition = {
    methods: ["post"],
    url: '/comps/{comp}/settings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::updateCompetitionSettings
* @see app/Http/Controllers/CompetitionController.php:83
* @route '/comps/{comp}/settings'
*/
updateCompetitionSettings.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateCompetitionSettings.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::updateCompetitionSettings
* @see app/Http/Controllers/CompetitionController.php:83
* @route '/comps/{comp}/settings'
*/
updateCompetitionSettings.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateCompetitionSettings.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionController::updateCompetitionScoringSettings
* @see app/Http/Controllers/CompetitionController.php:129
* @route '/comps/{comp}/settings/scoring'
*/
export const updateCompetitionScoringSettings = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateCompetitionScoringSettings.url(args, options),
    method: 'post',
})

updateCompetitionScoringSettings.definition = {
    methods: ["post"],
    url: '/comps/{comp}/settings/scoring',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::updateCompetitionScoringSettings
* @see app/Http/Controllers/CompetitionController.php:129
* @route '/comps/{comp}/settings/scoring'
*/
updateCompetitionScoringSettings.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateCompetitionScoringSettings.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::updateCompetitionScoringSettings
* @see app/Http/Controllers/CompetitionController.php:129
* @route '/comps/{comp}/settings/scoring'
*/
updateCompetitionScoringSettings.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateCompetitionScoringSettings.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionController::deleteComp
* @see app/Http/Controllers/CompetitionController.php:320
* @route '/comps/{comp}/delete'
*/
export const deleteComp = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deleteComp.url(args, options),
    method: 'post',
})

deleteComp.definition = {
    methods: ["post"],
    url: '/comps/{comp}/delete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::deleteComp
* @see app/Http/Controllers/CompetitionController.php:320
* @route '/comps/{comp}/delete'
*/
deleteComp.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return deleteComp.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::deleteComp
* @see app/Http/Controllers/CompetitionController.php:320
* @route '/comps/{comp}/delete'
*/
deleteComp.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deleteComp.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionController::getCompetitionAccounts
* @see app/Http/Controllers/CompetitionController.php:192
* @route '/comps/{comp}/accounts'
*/
export const getCompetitionAccounts = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCompetitionAccounts.url(args, options),
    method: 'get',
})

getCompetitionAccounts.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/accounts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionController::getCompetitionAccounts
* @see app/Http/Controllers/CompetitionController.php:192
* @route '/comps/{comp}/accounts'
*/
getCompetitionAccounts.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return getCompetitionAccounts.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::getCompetitionAccounts
* @see app/Http/Controllers/CompetitionController.php:192
* @route '/comps/{comp}/accounts'
*/
getCompetitionAccounts.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCompetitionAccounts.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionController::getCompetitionAccounts
* @see app/Http/Controllers/CompetitionController.php:192
* @route '/comps/{comp}/accounts'
*/
getCompetitionAccounts.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCompetitionAccounts.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetitionController::getCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:219
* @route '/comps/{comp}/account/{account}'
*/
export const getCompetitionAccount = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCompetitionAccount.url(args, options),
    method: 'get',
})

getCompetitionAccount.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/account/{account}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionController::getCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:219
* @route '/comps/{comp}/account/{account}'
*/
getCompetitionAccount.url = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            account: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        account: typeof args.account === 'object'
        ? args.account.id
        : args.account,
    }

    return getCompetitionAccount.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{account}', parsedArgs.account.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::getCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:219
* @route '/comps/{comp}/account/{account}'
*/
getCompetitionAccount.get = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCompetitionAccount.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionController::getCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:219
* @route '/comps/{comp}/account/{account}'
*/
getCompetitionAccount.head = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCompetitionAccount.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetitionController::editCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:239
* @route '/comps/{comp}/account/{account}'
*/
export const editCompetitionAccount = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editCompetitionAccount.url(args, options),
    method: 'post',
})

editCompetitionAccount.definition = {
    methods: ["post"],
    url: '/comps/{comp}/account/{account}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::editCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:239
* @route '/comps/{comp}/account/{account}'
*/
editCompetitionAccount.url = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            account: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        account: typeof args.account === 'object'
        ? args.account.id
        : args.account,
    }

    return editCompetitionAccount.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{account}', parsedArgs.account.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::editCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:239
* @route '/comps/{comp}/account/{account}'
*/
editCompetitionAccount.post = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editCompetitionAccount.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionController::deleteCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:252
* @route '/comps/{comp}/account/{account}'
*/
export const deleteCompetitionAccount = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCompetitionAccount.url(args, options),
    method: 'delete',
})

deleteCompetitionAccount.definition = {
    methods: ["delete"],
    url: '/comps/{comp}/account/{account}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CompetitionController::deleteCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:252
* @route '/comps/{comp}/account/{account}'
*/
deleteCompetitionAccount.url = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            account: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        account: typeof args.account === 'object'
        ? args.account.id
        : args.account,
    }

    return deleteCompetitionAccount.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{account}', parsedArgs.account.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::deleteCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:252
* @route '/comps/{comp}/account/{account}'
*/
deleteCompetitionAccount.delete = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCompetitionAccount.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\CompetitionController::inviteCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:159
* @route '/comps/{comp}/accounts/invitee'
*/
export const inviteCompetitionAccount = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: inviteCompetitionAccount.url(args, options),
    method: 'post',
})

inviteCompetitionAccount.definition = {
    methods: ["post"],
    url: '/comps/{comp}/accounts/invitee',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::inviteCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:159
* @route '/comps/{comp}/accounts/invitee'
*/
inviteCompetitionAccount.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return inviteCompetitionAccount.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::inviteCompetitionAccount
* @see app/Http/Controllers/CompetitionController.php:159
* @route '/comps/{comp}/accounts/invitee'
*/
inviteCompetitionAccount.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: inviteCompetitionAccount.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionController::resetSercWriterAccountPassword
* @see app/Http/Controllers/CompetitionController.php:0
* @route '/comps/{comp}/account/serc-writer/new-password'
*/
export const resetSercWriterAccountPassword = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resetSercWriterAccountPassword.url(args, options),
    method: 'post',
})

resetSercWriterAccountPassword.definition = {
    methods: ["post"],
    url: '/comps/{comp}/account/serc-writer/new-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::resetSercWriterAccountPassword
* @see app/Http/Controllers/CompetitionController.php:0
* @route '/comps/{comp}/account/serc-writer/new-password'
*/
resetSercWriterAccountPassword.url = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: args.comp,
    }

    return resetSercWriterAccountPassword.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::resetSercWriterAccountPassword
* @see app/Http/Controllers/CompetitionController.php:0
* @route '/comps/{comp}/account/serc-writer/new-password'
*/
resetSercWriterAccountPassword.post = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resetSercWriterAccountPassword.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionController::events
* @see app/Http/Controllers/CompetitionController.php:43
* @route '/comps/{comp}/events'
*/
export const events = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: events.url(args, options),
    method: 'get',
})

events.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionController::events
* @see app/Http/Controllers/CompetitionController.php:43
* @route '/comps/{comp}/events'
*/
events.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return events.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::events
* @see app/Http/Controllers/CompetitionController.php:43
* @route '/comps/{comp}/events'
*/
events.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: events.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionController::events
* @see app/Http/Controllers/CompetitionController.php:43
* @route '/comps/{comp}/events'
*/
events.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: events.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetitionController::teams
* @see app/Http/Controllers/CompetitionController.php:51
* @route '/comps/{comp}/teams'
*/
export const teams = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: teams.url(args, options),
    method: 'get',
})

teams.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/teams',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionController::teams
* @see app/Http/Controllers/CompetitionController.php:51
* @route '/comps/{comp}/teams'
*/
teams.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return teams.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::teams
* @see app/Http/Controllers/CompetitionController.php:51
* @route '/comps/{comp}/teams'
*/
teams.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: teams.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionController::teams
* @see app/Http/Controllers/CompetitionController.php:51
* @route '/comps/{comp}/teams'
*/
teams.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: teams.url(args, options),
    method: 'head',
})

const CompetitionController = { create, createPost, view, activityLog, createCompetitionStats, updateCompetitionSettings, updateCompetitionScoringSettings, deleteComp, getCompetitionAccounts, getCompetitionAccount, editCompetitionAccount, deleteCompetitionAccount, inviteCompetitionAccount, resetSercWriterAccountPassword, events, teams }

export default CompetitionController