import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\CompetitionController::newPassword
* @see app/Http/Controllers/CompetitionController.php:0
* @route '/comps/{comp}/account/serc-writer/new-password'
*/
export const newPassword = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: newPassword.url(args, options),
    method: 'post',
})

newPassword.definition = {
    methods: ["post"],
    url: '/comps/{comp}/account/serc-writer/new-password',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\CompetitionController::newPassword
* @see app/Http/Controllers/CompetitionController.php:0
* @route '/comps/{comp}/account/serc-writer/new-password'
*/
newPassword.url = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return newPassword.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\CompetitionController::newPassword
* @see app/Http/Controllers/CompetitionController.php:0
* @route '/comps/{comp}/account/serc-writer/new-password'
*/
newPassword.post = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: newPassword.url(args, options),
    method: 'post',
})

const sercWriter = {
    newPassword: Object.assign(newPassword, newPassword),
}

export default sercWriter