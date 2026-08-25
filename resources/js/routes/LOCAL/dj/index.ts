import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see routes/digitaljudge.php:201
* @route '//judge.localhost/toggle-head-ref'
*/
export const toggleHeadRef = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: toggleHeadRef.url(options),
    method: 'get',
})

toggleHeadRef.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/toggle-head-ref',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/digitaljudge.php:201
* @route '//judge.localhost/toggle-head-ref'
*/
toggleHeadRef.url = (options?: RouteQueryOptions) => {
    return toggleHeadRef.definition.url + queryParams(options)
}

/**
* @see routes/digitaljudge.php:201
* @route '//judge.localhost/toggle-head-ref'
*/
toggleHeadRef.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: toggleHeadRef.url(options),
    method: 'get',
})

/**
* @see routes/digitaljudge.php:201
* @route '//judge.localhost/toggle-head-ref'
*/
toggleHeadRef.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: toggleHeadRef.url(options),
    method: 'head',
})

const dj = {
    toggleHeadRef: Object.assign(toggleHeadRef, toggleHeadRef),
}

export default dj