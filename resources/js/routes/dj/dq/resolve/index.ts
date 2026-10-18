import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::list
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:281
* @route '//judge.localhost/dq/resolve/list'
*/
export const list = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: list.url(options),
    method: 'get',
})

list.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/dq/resolve/list',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::list
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:281
* @route '//judge.localhost/dq/resolve/list'
*/
list.url = (options?: RouteQueryOptions) => {
    return list.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::list
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:281
* @route '//judge.localhost/dq/resolve/list'
*/
list.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: list.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\DJDQController::list
* @see app/Http/Controllers/DigitalJudge/DJDQController.php:281
* @route '//judge.localhost/dq/resolve/list'
*/
list.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: list.url(options),
    method: 'head',
})

const resolve = {
    list: Object.assign(list, list),
}

export default resolve