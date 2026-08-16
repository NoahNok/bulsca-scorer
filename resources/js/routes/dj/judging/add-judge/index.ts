import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::post
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:259
* @route '//judge.localhost/judging/add-judge'
*/
export const post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '//judge.localhost/judging/add-judge',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::post
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:259
* @route '//judge.localhost/judging/add-judge'
*/
post.url = (options?: RouteQueryOptions) => {
    return post.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJJudgingController::post
* @see app/Http/Controllers/DigitalJudge/DJJudgingController.php:259
* @route '//judge.localhost/judging/add-judge'
*/
post.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(options),
    method: 'post',
})

const addJudge = {
    post: Object.assign(post, post),
}

export default addJudge