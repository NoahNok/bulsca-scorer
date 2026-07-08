import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Organisation\OrganisationController::post
* @see app/Http/Controllers/Organisation/OrganisationController.php:131
* @route '/organisation/{organisation}/accounts'
*/
export const post = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/accounts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::post
* @see app/Http/Controllers/Organisation/OrganisationController.php:131
* @route '/organisation/{organisation}/accounts'
*/
post.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return post.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::post
* @see app/Http/Controllers/Organisation/OrganisationController.php:131
* @route '/organisation/{organisation}/accounts'
*/
post.post = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::view
* @see app/Http/Controllers/Organisation/OrganisationController.php:165
* @route '/organisation/{organisation}/accounts/{account}'
*/
export const view = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

view.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/accounts/{account}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::view
* @see app/Http/Controllers/Organisation/OrganisationController.php:165
* @route '/organisation/{organisation}/accounts/{account}'
*/
view.url = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            account: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        account: typeof args.account === 'object'
        ? args.account.id
        : args.account,
    }

    return view.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{account}', parsedArgs.account.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::view
* @see app/Http/Controllers/Organisation/OrganisationController.php:165
* @route '/organisation/{organisation}/accounts/{account}'
*/
view.get = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: view.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::view
* @see app/Http/Controllers/Organisation/OrganisationController.php:165
* @route '/organisation/{organisation}/accounts/{account}'
*/
view.head = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: view.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:185
* @route '/organisation/{organisation}/accounts/{account}'
*/
export const edit = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: edit.url(args, options),
    method: 'post',
})

edit.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/accounts/{account}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:185
* @route '/organisation/{organisation}/accounts/{account}'
*/
edit.url = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            account: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        account: typeof args.account === 'object'
        ? args.account.id
        : args.account,
    }

    return edit.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{account}', parsedArgs.account.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:185
* @route '/organisation/{organisation}/accounts/{account}'
*/
edit.post = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: edit.url(args, options),
    method: 'post',
})

const accounts = {
    post: Object.assign(post, post),
    view: Object.assign(view, view),
    edit: Object.assign(edit, edit),
}

export default accounts