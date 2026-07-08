import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::post
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:338
* @route '//judge.localhost/judging/overall-comments'
*/
export const post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '//judge.localhost/judging/overall-comments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::post
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:338
* @route '//judge.localhost/judging/overall-comments'
*/
post.url = (options?: RouteQueryOptions) => {
    return post.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::post
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:338
* @route '//judge.localhost/judging/overall-comments'
*/
post.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

const overallComments = {
    post: Object.assign(post, post),
}

export default overallComments