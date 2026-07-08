import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
import imageE9c754 from './image'
/**
* @see \App\Http\Controllers\SERCController::hide
* @see app/Http/Controllers/SERCController.php:265
* @route '/comps/{comp}/events/sercs/{serc}/hide'
*/
export const hide = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

hide.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/events/sercs/{serc}/hide',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SERCController::hide
* @see app/Http/Controllers/SERCController.php:265
* @route '/comps/{comp}/events/sercs/{serc}/hide'
*/
hide.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return hide.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::hide
* @see app/Http/Controllers/SERCController.php:265
* @route '/comps/{comp}/events/sercs/{serc}/hide'
*/
hide.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hide.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SERCController::hide
* @see app/Http/Controllers/SERCController.php:265
* @route '/comps/{comp}/events/sercs/{serc}/hide'
*/
hide.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: hide.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\SERCController::image
* @see app/Http/Controllers/SERCController.php:271
* @route '/comps/{comp}/events/sercs/{serc}/image'
*/
export const image = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: image.url(args, options),
    method: 'post',
})

image.definition = {
    methods: ["post"],
    url: '/comps/{comp}/events/sercs/{serc}/image',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\SERCController::image
* @see app/Http/Controllers/SERCController.php:271
* @route '/comps/{comp}/events/sercs/{serc}/image'
*/
image.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return image.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\SERCController::image
* @see app/Http/Controllers/SERCController.php:271
* @route '/comps/{comp}/events/sercs/{serc}/image'
*/
image.post = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: image.url(args, options),
    method: 'post',
})

const sercs = {
    hide: Object.assign(hide, hide),
    image: Object.assign(image, imageE9c754),
}

export default sercs