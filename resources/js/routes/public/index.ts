import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import results8ded7a from './results'
/**
* @see \App\Http\Controllers\PublicResultsController::results
* @see app/Http/Controllers/PublicResultsController.php:25
* @route '//results.localhost'
*/
export const results = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(options),
    method: 'get',
})

results.definition = {
    methods: ["get","head"],
    url: '//results.localhost',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::results
* @see app/Http/Controllers/PublicResultsController.php:25
* @route '//results.localhost'
*/
results.url = (options?: RouteQueryOptions) => {
    return results.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::results
* @see app/Http/Controllers/PublicResultsController.php:25
* @route '//results.localhost'
*/
results.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::results
* @see app/Http/Controllers/PublicResultsController.php:25
* @route '//results.localhost'
*/
results.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: results.url(options),
    method: 'head',
})

const publicMethod = {
    results: Object.assign(results, results8ded7a),
}

export default publicMethod