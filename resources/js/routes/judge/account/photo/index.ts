import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::destroy
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:61
* @route '//judge.localhost/v2/account/photo'
*/
export const destroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '//judge.localhost/v2/account/photo',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::destroy
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:61
* @route '//judge.localhost/v2/account/photo'
*/
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\Auth\AccountController::destroy
* @see app/Http/Controllers/DigitalJudge/Auth/AccountController.php:61
* @route '//judge.localhost/v2/account/photo'
*/
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

const photo = {
    destroy: Object.assign(destroy, destroy),
}

export default photo