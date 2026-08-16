import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults, validateParameters } from './../wayfinder'
/**
* @see \App\Http\Controllers\LiveController::live
* @see app/Http/Controllers/LiveController.php:36
* @route '//live.localhost'
*/
export const live = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: live.url(options),
    method: 'get',
})

live.definition = {
    methods: ["get","head"],
    url: '//live.localhost',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\LiveController::live
* @see app/Http/Controllers/LiveController.php:36
* @route '//live.localhost'
*/
live.url = (options?: RouteQueryOptions) => {
    return live.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\LiveController::live
* @see app/Http/Controllers/LiveController.php:36
* @route '//live.localhost'
*/
live.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: live.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\LiveController::live
* @see app/Http/Controllers/LiveController.php:36
* @route '//live.localhost'
*/
live.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: live.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::whatif
* @see app/Http/Controllers/WhatIf/WhatIfController.php:30
* @route '//whatif.localhost'
*/
export const whatif = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: whatif.url(options),
    method: 'get',
})

whatif.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::whatif
* @see app/Http/Controllers/WhatIf/WhatIfController.php:30
* @route '//whatif.localhost'
*/
whatif.url = (options?: RouteQueryOptions) => {
    return whatif.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::whatif
* @see app/Http/Controllers/WhatIf/WhatIfController.php:30
* @route '//whatif.localhost'
*/
whatif.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: whatif.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::whatif
* @see app/Http/Controllers/WhatIf/WhatIfController.php:30
* @route '//whatif.localhost'
*/
whatif.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: whatif.url(options),
    method: 'head',
})

/**
* @see \Laravel\Telescope\Http\Controllers\HomeController::telescope
* @see vendor/laravel/telescope/src/Http/Controllers/HomeController.php:15
* @route '/telescope/{view?}'
*/
export const telescope = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: telescope.url(args, options),
    method: 'get',
})

telescope.definition = {
    methods: ["get","head"],
    url: '/telescope/{view?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Telescope\Http\Controllers\HomeController::telescope
* @see vendor/laravel/telescope/src/Http/Controllers/HomeController.php:15
* @route '/telescope/{view?}'
*/
telescope.url = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { view: args }
    }

    if (Array.isArray(args)) {
        args = {
            view: args[0],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "view",
    ])

    const parsedArgs = {
        view: args?.view,
    }

    return telescope.definition.url
            .replace('{view?}', parsedArgs.view?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Laravel\Telescope\Http\Controllers\HomeController::telescope
* @see vendor/laravel/telescope/src/Http/Controllers/HomeController.php:15
* @route '/telescope/{view?}'
*/
telescope.get = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: telescope.url(args, options),
    method: 'get',
})

/**
* @see \Laravel\Telescope\Http\Controllers\HomeController::telescope
* @see vendor/laravel/telescope/src/Http/Controllers/HomeController.php:15
* @route '/telescope/{view?}'
*/
telescope.head = (args?: { view?: string | number } | [view: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: telescope.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:77
* @route '/'
*/
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '/',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:77
* @route '/'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see routes/web.php:77
* @route '/'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see routes/web.php:77
* @route '/'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/comps'
*/
export const comps = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: comps.url(options),
    method: 'get',
})

comps.definition = {
    methods: ["get","head","post","put","patch","delete","options"],
    url: '/comps',
} satisfies RouteDefinition<["get","head","post","put","patch","delete","options"]>

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/comps'
*/
comps.url = (options?: RouteQueryOptions) => {
    return comps.definition.url + queryParams(options)
}

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/comps'
*/
comps.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: comps.url(options),
    method: 'get',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/comps'
*/
comps.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: comps.url(options),
    method: 'head',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/comps'
*/
comps.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: comps.url(options),
    method: 'post',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/comps'
*/
comps.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: comps.url(options),
    method: 'put',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/comps'
*/
comps.patch = (options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: comps.url(options),
    method: 'patch',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/comps'
*/
comps.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: comps.url(options),
    method: 'delete',
})

/**
* @see \Illuminate\Routing\RedirectController::__invoke
* @see vendor/laravel/framework/src/Illuminate/Routing/RedirectController.php:19
* @route '/comps'
*/
comps.options = (options?: RouteQueryOptions): RouteDefinition<'options'> => ({
    url: comps.url(options),
    method: 'options',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::explore
* @see app/Http/Controllers/Landing/LandingController.php:15
* @route '/explore'
*/
export const explore = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: explore.url(options),
    method: 'get',
})

explore.definition = {
    methods: ["get","head"],
    url: '/explore',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\LandingController::explore
* @see app/Http/Controllers/Landing/LandingController.php:15
* @route '/explore'
*/
explore.url = (options?: RouteQueryOptions) => {
    return explore.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\LandingController::explore
* @see app/Http/Controllers/Landing/LandingController.php:15
* @route '/explore'
*/
explore.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: explore.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::explore
* @see app/Http/Controllers/Landing/LandingController.php:15
* @route '/explore'
*/
explore.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: explore.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::search
* @see app/Http/Controllers/Landing/LandingController.php:52
* @route '/search/{search}'
*/
export const search = (args: { search: string | number } | [search: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(args, options),
    method: 'get',
})

search.definition = {
    methods: ["get","head"],
    url: '/search/{search}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Landing\LandingController::search
* @see app/Http/Controllers/Landing/LandingController.php:52
* @route '/search/{search}'
*/
search.url = (args: { search: string | number } | [search: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { search: args }
    }

    if (Array.isArray(args)) {
        args = {
            search: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        search: args.search,
    }

    return search.definition.url
            .replace('{search}', parsedArgs.search.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Landing\LandingController::search
* @see app/Http/Controllers/Landing/LandingController.php:52
* @route '/search/{search}'
*/
search.get = (args: { search: string | number } | [search: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Landing\LandingController::search
* @see app/Http/Controllers/Landing/LandingController.php:52
* @route '/search/{search}'
*/
search.head = (args: { search: string | number } | [search: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: search.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\RegisteredUserController::register
* @see app/Http/Controllers/Auth/RegisteredUserController.php:21
* @route '/register'
*/
export const register = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

register.definition = {
    methods: ["get","head"],
    url: '/register',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\RegisteredUserController::register
* @see app/Http/Controllers/Auth/RegisteredUserController.php:21
* @route '/register'
*/
register.url = (options?: RouteQueryOptions) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\RegisteredUserController::register
* @see app/Http/Controllers/Auth/RegisteredUserController.php:21
* @route '/register'
*/
register.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\RegisteredUserController::register
* @see app/Http/Controllers/Auth/RegisteredUserController.php:21
* @route '/register'
*/
register.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: register.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:18
* @route '/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:18
* @route '/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:18
* @route '/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:18
* @route '/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:38
* @route '/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logout.url(options),
    method: 'get',
})

logout.definition = {
    methods: ["get","head"],
    url: '/logout',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:38
* @route '/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:38
* @route '/logout'
*/
logout.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logout.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:38
* @route '/logout'
*/
logout.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logout.url(options),
    method: 'head',
})

