import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import sercWriter from './serc-writer'
/**
* @see \App\Http\Controllers\CompetitionController::view
* @see app/Http/Controllers/CompetitionController.php:219
* @route '/comps/{comp}/account/{account}'
*/
export const view = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/account/{account}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\CompetitionController::view
* @see app/Http/Controllers/CompetitionController.php:219
* @route '/comps/{comp}/account/{account}'
*/
view.url = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return view.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{account}', parsedArgs.account.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::view
* @see app/Http/Controllers/CompetitionController.php:219
* @route '/comps/{comp}/account/{account}'
*/
view.get = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\CompetitionController::view
* @see app/Http/Controllers/CompetitionController.php:219
* @route '/comps/{comp}/account/{account}'
*/
view.head = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\CompetitionController::edit
* @see app/Http/Controllers/CompetitionController.php:239
* @route '/comps/{comp}/account/{account}'
*/
export const edit = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: edit.url(args, options),
    method: 'post',
})

edit.definition = {
    methods: ["post"],
    url: '/comps/{comp}/account/{account}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::edit
* @see app/Http/Controllers/CompetitionController.php:239
* @route '/comps/{comp}/account/{account}'
*/
edit.url = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return edit.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{account}', parsedArgs.account.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::edit
* @see app/Http/Controllers/CompetitionController.php:239
* @route '/comps/{comp}/account/{account}'
*/
edit.post = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: edit.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\CompetitionController::deleteMethod
* @see app/Http/Controllers/CompetitionController.php:252
* @route '/comps/{comp}/account/{account}'
*/
export const deleteMethod = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/comps/{comp}/account/{account}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\CompetitionController::deleteMethod
* @see app/Http/Controllers/CompetitionController.php:252
* @route '/comps/{comp}/account/{account}'
*/
deleteMethod.url = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return deleteMethod.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{account}', parsedArgs.account.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::deleteMethod
* @see app/Http/Controllers/CompetitionController.php:252
* @route '/comps/{comp}/account/{account}'
*/
deleteMethod.delete = (args: { comp: number | { id: number }, account: number | { id: number } } | [comp: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\CompetitionController::invite
* @see app/Http/Controllers/CompetitionController.php:159
* @route '/comps/{comp}/accounts/invitee'
*/
export const invite = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: invite.url(args, options),
    method: 'post',
})

invite.definition = {
    methods: ["post"],
    url: '/comps/{comp}/accounts/invitee',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::invite
* @see app/Http/Controllers/CompetitionController.php:159
* @route '/comps/{comp}/accounts/invitee'
*/
invite.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return invite.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::invite
* @see app/Http/Controllers/CompetitionController.php:159
* @route '/comps/{comp}/accounts/invitee'
*/
invite.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: invite.url(args, options),
    method: 'post',
})

const accounts = {
    view: Object.assign(view, view),
    edit: Object.assign(edit, edit),
    delete: Object.assign(deleteMethod, deleteMethod),
    invite: Object.assign(invite, invite),
    sercWriter: Object.assign(sercWriter, sercWriter),
}

export default accounts