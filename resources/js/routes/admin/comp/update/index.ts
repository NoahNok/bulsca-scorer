import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminController::post
* @see app/Http/Controllers/AdminController.php:56
* @route '/admin/competition/{comp}/update'
*/
export const post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '/admin/competition/{comp}/update',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::post
* @see app/Http/Controllers/AdminController.php:56
* @route '/admin/competition/{comp}/update'
*/
post.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return post.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::post
* @see app/Http/Controllers/AdminController.php:56
* @route '/admin/competition/{comp}/update'
*/
post.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminController::userPassword
* @see app/Http/Controllers/AdminController.php:86
* @route '/admin/competition/{comp}/updateUser'
*/
export const userPassword = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: userPassword.url(args, options),
    method: 'post',
})

userPassword.definition = {
    methods: ["post"],
    url: '/admin/competition/{comp}/updateUser',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::userPassword
* @see app/Http/Controllers/AdminController.php:86
* @route '/admin/competition/{comp}/updateUser'
*/
userPassword.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return userPassword.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::userPassword
* @see app/Http/Controllers/AdminController.php:86
* @route '/admin/competition/{comp}/updateUser'
*/
userPassword.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: userPassword.url(args, options),
    method: 'post',
})

const update = {
    post: Object.assign(post, post),
    userPassword: Object.assign(userPassword, userPassword),
}

export default update