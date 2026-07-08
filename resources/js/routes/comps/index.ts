import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import create4f58d6 from './create'
import view381131 from './view'
import settings69f00b from './settings'
import accountsDb024e from './accounts'
import leagues from './leagues'
import events735790 from './events'
import teamsE68ab5 from './teams'
import entities30f6b9 from './entities'
import results8ded7a from './results'
import heats_and_drawsF391b8 from './heats_and_draws'
import printables76a3b6 from './printables'
import notifications from './notifications'
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
* @see \App\Http\Controllers\CompetitionController::createStats
* @see app/Http/Controllers/CompetitionController.php:71
* @route '/comps/{comp}/create-stats'
*/
export const createStats = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createStats.url(args, options),
    method: 'get',
})

createStats.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/create-stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionController::createStats
* @see app/Http/Controllers/CompetitionController.php:71
* @route '/comps/{comp}/create-stats'
*/
createStats.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return createStats.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::createStats
* @see app/Http/Controllers/CompetitionController.php:71
* @route '/comps/{comp}/create-stats'
*/
createStats.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createStats.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionController::createStats
* @see app/Http/Controllers/CompetitionController.php:71
* @route '/comps/{comp}/create-stats'
*/
createStats.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: createStats.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetitionController::settings
* @see app/Http/Controllers/CompetitionController.php:83
* @route '/comps/{comp}/settings'
*/
export const settings = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: settings.url(args, options),
    method: 'post',
})

settings.definition = {
    methods: ["post"],
    url: '/comps/{comp}/settings',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::settings
* @see app/Http/Controllers/CompetitionController.php:83
* @route '/comps/{comp}/settings'
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
* @see \App\Http\Controllers\CompetitionController::settings
* @see app/Http/Controllers/CompetitionController.php:83
* @route '/comps/{comp}/settings'
*/
settings.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: settings.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionController::deleteMethod
* @see app/Http/Controllers/CompetitionController.php:320
* @route '/comps/{comp}/delete'
*/
export const deleteMethod = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deleteMethod.url(args, options),
    method: 'post',
})

deleteMethod.definition = {
    methods: ["post"],
    url: '/comps/{comp}/delete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::deleteMethod
* @see app/Http/Controllers/CompetitionController.php:320
* @route '/comps/{comp}/delete'
*/
deleteMethod.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return deleteMethod.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::deleteMethod
* @see app/Http/Controllers/CompetitionController.php:320
* @route '/comps/{comp}/delete'
*/
deleteMethod.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deleteMethod.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionController::accounts
* @see app/Http/Controllers/CompetitionController.php:192
* @route '/comps/{comp}/accounts'
*/
export const accounts = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: accounts.url(args, options),
    method: 'get',
})

accounts.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/accounts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionController::accounts
* @see app/Http/Controllers/CompetitionController.php:192
* @route '/comps/{comp}/accounts'
*/
accounts.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return accounts.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::accounts
* @see app/Http/Controllers/CompetitionController.php:192
* @route '/comps/{comp}/accounts'
*/
accounts.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: accounts.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionController::accounts
* @see app/Http/Controllers/CompetitionController.php:192
* @route '/comps/{comp}/accounts'
*/
accounts.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: accounts.url(args, options),
    method: 'head',
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

/**
* @see \App\Http\Controllers\EntityController::entities
* @see app/Http/Controllers/EntityController.php:18
* @route '/comps/{comp}/entities'
*/
export const entities = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: entities.url(args, options),
    method: 'get',
})

entities.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/entities',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\EntityController::entities
* @see app/Http/Controllers/EntityController.php:18
* @route '/comps/{comp}/entities'
*/
entities.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return entities.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\EntityController::entities
* @see app/Http/Controllers/EntityController.php:18
* @route '/comps/{comp}/entities'
*/
entities.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: entities.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\EntityController::entities
* @see app/Http/Controllers/EntityController.php:18
* @route '/comps/{comp}/entities'
*/
entities.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: entities.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\OverallResultsController::results
* @see app/Http/Controllers/OverallResultsController.php:76
* @route '/comps/{comp}/results'
*/
export const results = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

results.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/results',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\OverallResultsController::results
* @see app/Http/Controllers/OverallResultsController.php:76
* @route '/comps/{comp}/results'
*/
results.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return results.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\OverallResultsController::results
* @see app/Http/Controllers/OverallResultsController.php:76
* @route '/comps/{comp}/results'
*/
results.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\OverallResultsController::results
* @see app/Http/Controllers/OverallResultsController.php:76
* @route '/comps/{comp}/results'
*/
results.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: results.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Orders\HeatController::heats_and_draws
* @see app/Http/Controllers/Orders/HeatController.php:19
* @route '/comps/{comp}/heats-and-draws'
*/
export const heats_and_draws = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heats_and_draws.url(args, options),
    method: 'get',
})

heats_and_draws.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/heats-and-draws',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Orders\HeatController::heats_and_draws
* @see app/Http/Controllers/Orders/HeatController.php:19
* @route '/comps/{comp}/heats-and-draws'
*/
heats_and_draws.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return heats_and_draws.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Orders\HeatController::heats_and_draws
* @see app/Http/Controllers/Orders/HeatController.php:19
* @route '/comps/{comp}/heats-and-draws'
*/
heats_and_draws.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heats_and_draws.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Orders\HeatController::heats_and_draws
* @see app/Http/Controllers/Orders/HeatController.php:19
* @route '/comps/{comp}/heats-and-draws'
*/
heats_and_draws.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: heats_and_draws.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PrintableController::printables
* @see app/Http/Controllers/PrintableController.php:13
* @route '/comps/{comp}/printables'
*/
export const printables = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printables.url(args, options),
    method: 'get',
})

printables.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/printables',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PrintableController::printables
* @see app/Http/Controllers/PrintableController.php:13
* @route '/comps/{comp}/printables'
*/
printables.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return printables.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PrintableController::printables
* @see app/Http/Controllers/PrintableController.php:13
* @route '/comps/{comp}/printables'
*/
printables.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printables.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PrintableController::printables
* @see app/Http/Controllers/PrintableController.php:13
* @route '/comps/{comp}/printables'
*/
printables.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: printables.url(args, options),
    method: 'head',
})

const comps = {
    create: Object.assign(create, create4f58d6),
    view: Object.assign(view, view381131),
    activityLog: Object.assign(activityLog, activityLog),
    createStats: Object.assign(createStats, createStats),
    settings: Object.assign(settings, settings69f00b),
    delete: Object.assign(deleteMethod, deleteMethod),
    accounts: Object.assign(accounts, accountsDb024e),
    leagues: Object.assign(leagues, leagues),
    events: Object.assign(events, events735790),
    teams: Object.assign(teams, teamsE68ab5),
    entities: Object.assign(entities, entities30f6b9),
    results: Object.assign(results, results8ded7a),
    heats_and_draws: Object.assign(heats_and_draws, heats_and_drawsF391b8),
    printables: Object.assign(printables, printables76a3b6),
    notifications: Object.assign(notifications, notifications),
}

export default comps