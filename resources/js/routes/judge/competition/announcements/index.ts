import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\AnnouncementController::store
* @see app/Http/Controllers/DigitalJudge/AnnouncementController.php:28
* @route '//judge.localhost/v2/{competition}/announcements'
*/
export const store = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/announcements',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\AnnouncementController::store
* @see app/Http/Controllers/DigitalJudge/AnnouncementController.php:28
* @route '//judge.localhost/v2/{competition}/announcements'
*/
store.url = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { competition: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { competition: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            competition: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
    }

    return store.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\AnnouncementController::store
* @see app/Http/Controllers/DigitalJudge/AnnouncementController.php:28
* @route '//judge.localhost/v2/{competition}/announcements'
*/
store.post = (args: { competition: number | { id: number } } | [competition: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\AnnouncementController::withdraw
* @see app/Http/Controllers/DigitalJudge/AnnouncementController.php:47
* @route '//judge.localhost/v2/{competition}/announcements/{announcement}/withdraw'
*/
export const withdraw = (args: { competition: number | { id: number }, announcement: number | { id: number } } | [competition: number | { id: number }, announcement: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: withdraw.url(args, options),
    method: 'post',
})

withdraw.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/announcements/{announcement}/withdraw',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\AnnouncementController::withdraw
* @see app/Http/Controllers/DigitalJudge/AnnouncementController.php:47
* @route '//judge.localhost/v2/{competition}/announcements/{announcement}/withdraw'
*/
withdraw.url = (args: { competition: number | { id: number }, announcement: number | { id: number } } | [competition: number | { id: number }, announcement: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            announcement: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        announcement: typeof args.announcement === 'object'
        ? args.announcement.id
        : args.announcement,
    }

    return withdraw.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{announcement}', parsedArgs.announcement.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\AnnouncementController::withdraw
* @see app/Http/Controllers/DigitalJudge/AnnouncementController.php:47
* @route '//judge.localhost/v2/{competition}/announcements/{announcement}/withdraw'
*/
withdraw.post = (args: { competition: number | { id: number }, announcement: number | { id: number } } | [competition: number | { id: number }, announcement: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: withdraw.url(args, options),
    method: 'post',
})

const announcements = {
    store: Object.assign(store, store),
    withdraw: Object.assign(withdraw, withdraw),
}

export default announcements