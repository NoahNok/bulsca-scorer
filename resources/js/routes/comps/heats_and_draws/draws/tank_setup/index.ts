import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Orders\DrawController::post
* @see app/Http/Controllers/Orders/DrawController.php:95
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
export const post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '/comps/{comp}/heats-and-draws/draws/tank-setup',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Orders\DrawController::post
* @see app/Http/Controllers/Orders/DrawController.php:95
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
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
* @see \App\Http\Controllers\Orders\DrawController::post
* @see app/Http/Controllers/Orders/DrawController.php:95
* @route '/comps/{comp}/heats-and-draws/draws/tank-setup'
*/
post.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

const tank_setup = {
    post: Object.assign(post, post),
}

export default tank_setup