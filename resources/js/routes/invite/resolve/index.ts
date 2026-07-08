import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\AccountInviteController::newAccount
* @see app/Http/Controllers/AccountInviteController.php:92
* @route '/invite/{invite}/{email}/resolve/{resolution}/new-acc'
*/
export const newAccount = (args: { invite: string | { id: string }, email: string | number, resolution: string | number } | [invite: string | { id: string }, email: string | number, resolution: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: newAccount.url(args, options),
    method: 'post',
})

newAccount.definition = {
    methods: ["post"],
    url: '/invite/{invite}/{email}/resolve/{resolution}/new-acc',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AccountInviteController::newAccount
* @see app/Http/Controllers/AccountInviteController.php:92
* @route '/invite/{invite}/{email}/resolve/{resolution}/new-acc'
*/
newAccount.url = (args: { invite: string | { id: string }, email: string | number, resolution: string | number } | [invite: string | { id: string }, email: string | number, resolution: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            invite: args[0],
            email: args[1],
            resolution: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        invite: typeof args.invite === 'object'
        ? args.invite.id
        : args.invite,
        email: args.email,
        resolution: args.resolution,
    }

    return newAccount.definition.url
            .replace('{invite}', parsedArgs.invite.toString())
            .replace('{email}', parsedArgs.email.toString())
            .replace('{resolution}', parsedArgs.resolution.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AccountInviteController::newAccount
* @see app/Http/Controllers/AccountInviteController.php:92
* @route '/invite/{invite}/{email}/resolve/{resolution}/new-acc'
*/
newAccount.post = (args: { invite: string | { id: string }, email: string | number, resolution: string | number } | [invite: string | { id: string }, email: string | number, resolution: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: newAccount.url(args, options),
    method: 'post',
})

const resolve = {
    newAccount: Object.assign(newAccount, newAccount),
}

export default resolve