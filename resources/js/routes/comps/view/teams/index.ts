import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\TeamsController::editPost
* @see app/Http/Controllers/TeamsController.php:49
* @route '/comps/{comp}/teams/edit'
*/
export const editPost = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editPost.url(args, options),
    method: 'post',
})

editPost.definition = {
    methods: ["post"],
    url: '/comps/{comp}/teams/edit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\TeamsController::editPost
* @see app/Http/Controllers/TeamsController.php:49
* @route '/comps/{comp}/teams/edit'
*/
editPost.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return editPost.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TeamsController::editPost
* @see app/Http/Controllers/TeamsController.php:49
* @route '/comps/{comp}/teams/edit'
*/
editPost.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\TeamsController::deleteMethod
* @see app/Http/Controllers/TeamsController.php:125
* @route '/comps/{comp}/teams/delete'
*/
export const deleteMethod = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

deleteMethod.definition = {
    methods: ["delete"],
    url: '/comps/{comp}/teams/delete',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\TeamsController::deleteMethod
* @see app/Http/Controllers/TeamsController.php:125
* @route '/comps/{comp}/teams/delete'
*/
deleteMethod.url = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return deleteMethod.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\TeamsController::deleteMethod
* @see app/Http/Controllers/TeamsController.php:125
* @route '/comps/{comp}/teams/delete'
*/
deleteMethod.delete = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteMethod.url(args, options),
    method: 'delete',
})

const teams = {
    editPost: Object.assign(editPost, editPost),
    delete: Object.assign(deleteMethod, deleteMethod),
}

export default teams