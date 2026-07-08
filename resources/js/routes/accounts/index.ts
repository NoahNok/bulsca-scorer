import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\AccountController::search
* @see app/Http/Controllers/AccountController.php:10
* @route '/accounts/search/{email}'
*/
export const search = (args: { email: string | number } | [email: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(args, options),
    method: 'get',
})

search.definition = {
    methods: ["get","head"],
    url: '/accounts/search/{email}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AccountController::search
* @see app/Http/Controllers/AccountController.php:10
* @route '/accounts/search/{email}'
*/
search.url = (args: { email: string | number } | [email: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { email: args }
    }

    if (Array.isArray(args)) {
        args = {
            email: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        email: args.email,
    }

    return search.definition.url
            .replace('{email}', parsedArgs.email.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountController::search
* @see app/Http/Controllers/AccountController.php:10
* @route '/accounts/search/{email}'
*/
search.get = (args: { email: string | number } | [email: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AccountController::search
* @see app/Http/Controllers/AccountController.php:10
* @route '/accounts/search/{email}'
*/
search.head = (args: { email: string | number } | [email: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: search.url(args, options),
    method: 'head',
})

const accounts = {
    search: Object.assign(search, search),
}

export default accounts