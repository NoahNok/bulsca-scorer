import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::post
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:312
* @route '//judge.localhost/judging/tutorial'
*/
export const post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '//judge.localhost/judging/tutorial',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::post
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:312
* @route '//judge.localhost/judging/tutorial'
*/
post.url = (options?: RouteQueryOptions) => {
    return post.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::post
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:312
* @route '//judge.localhost/judging/tutorial'
*/
post.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

const tutorial = {
    post: Object.assign(post, post),
}

export default tutorial