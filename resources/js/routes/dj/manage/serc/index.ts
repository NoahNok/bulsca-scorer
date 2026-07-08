import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::post
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:23
* @route '//judge.localhost/manage/serc/{serc}'
*/
export const post = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

post.definition = {
    methods: ["post"],
    url: '//judge.localhost/manage/serc/{serc}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::post
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:23
* @route '//judge.localhost/manage/serc/{serc}'
*/
post.url = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { serc: args }
    }

    if (Array.isArray(args)) {
        args = {
            serc: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        serc: args.serc,
    }

    return post.definition.url
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\DJManageController::post
* @see app/Http/Controllers/DigitalJudge/DJManageController.php:23
* @route '//judge.localhost/manage/serc/{serc}'
*/
post.post = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: post.url(args, options),
    method: 'post',
})

const serc = {
    post: Object.assign(post, post),
}

export default serc