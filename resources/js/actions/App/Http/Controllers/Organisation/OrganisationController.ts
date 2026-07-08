import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Organisation\OrganisationController::index
* @see app/Http/Controllers/Organisation/OrganisationController.php:32
* @route '/organisation'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/organisation',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::index
* @see app/Http/Controllers/Organisation/OrganisationController.php:32
* @route '/organisation'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::index
* @see app/Http/Controllers/Organisation/OrganisationController.php:32
* @route '/organisation'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::index
* @see app/Http/Controllers/Organisation/OrganisationController.php:32
* @route '/organisation'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:40
* @route '/organisation/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/organisation/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:40
* @route '/organisation/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:40
* @route '/organisation/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::create
* @see app/Http/Controllers/Organisation/OrganisationController.php:40
* @route '/organisation/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::store
* @see app/Http/Controllers/Organisation/OrganisationController.php:48
* @route '/organisation'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/organisation',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::store
* @see app/Http/Controllers/Organisation/OrganisationController.php:48
* @route '/organisation'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::store
* @see app/Http/Controllers/Organisation/OrganisationController.php:48
* @route '/organisation'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::show
* @see app/Http/Controllers/Organisation/OrganisationController.php:71
* @route '/organisation/{organisation}'
*/
export const show = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::show
* @see app/Http/Controllers/Organisation/OrganisationController.php:71
* @route '/organisation/{organisation}'
*/
show.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return show.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::show
* @see app/Http/Controllers/Organisation/OrganisationController.php:71
* @route '/organisation/{organisation}'
*/
show.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::show
* @see app/Http/Controllers/Organisation/OrganisationController.php:71
* @route '/organisation/{organisation}'
*/
show.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:81
* @route '/organisation/{organisation}/edit'
*/
export const edit = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:81
* @route '/organisation/{organisation}/edit'
*/
edit.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return edit.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:81
* @route '/organisation/{organisation}/edit'
*/
edit.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::edit
* @see app/Http/Controllers/Organisation/OrganisationController.php:81
* @route '/organisation/{organisation}/edit'
*/
edit.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::update
* @see app/Http/Controllers/Organisation/OrganisationController.php:91
* @route '/organisation/{organisation}'
*/
export const update = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/organisation/{organisation}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::update
* @see app/Http/Controllers/Organisation/OrganisationController.php:91
* @route '/organisation/{organisation}'
*/
update.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return update.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::update
* @see app/Http/Controllers/Organisation/OrganisationController.php:91
* @route '/organisation/{organisation}'
*/
update.put = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::update
* @see app/Http/Controllers/Organisation/OrganisationController.php:91
* @route '/organisation/{organisation}'
*/
update.patch = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::destroy
* @see app/Http/Controllers/Organisation/OrganisationController.php:118
* @route '/organisation/{organisation}'
*/
export const destroy = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/organisation/{organisation}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::destroy
* @see app/Http/Controllers/Organisation/OrganisationController.php:118
* @route '/organisation/{organisation}'
*/
destroy.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return destroy.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::destroy
* @see app/Http/Controllers/Organisation/OrganisationController.php:118
* @route '/organisation/{organisation}'
*/
destroy.delete = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accounts
* @see app/Http/Controllers/Organisation/OrganisationController.php:124
* @route '/organisation/{organisation}/accounts'
*/
export const accounts = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: accounts.url(args, options),
    method: 'get',
})

accounts.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/accounts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accounts
* @see app/Http/Controllers/Organisation/OrganisationController.php:124
* @route '/organisation/{organisation}/accounts'
*/
accounts.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return accounts.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accounts
* @see app/Http/Controllers/Organisation/OrganisationController.php:124
* @route '/organisation/{organisation}/accounts'
*/
accounts.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: accounts.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accounts
* @see app/Http/Controllers/Organisation/OrganisationController.php:124
* @route '/organisation/{organisation}/accounts'
*/
accounts.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: accounts.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accountsPost
* @see app/Http/Controllers/Organisation/OrganisationController.php:131
* @route '/organisation/{organisation}/accounts'
*/
export const accountsPost = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: accountsPost.url(args, options),
    method: 'post',
})

accountsPost.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/accounts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accountsPost
* @see app/Http/Controllers/Organisation/OrganisationController.php:131
* @route '/organisation/{organisation}/accounts'
*/
accountsPost.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return accountsPost.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accountsPost
* @see app/Http/Controllers/Organisation/OrganisationController.php:131
* @route '/organisation/{organisation}/accounts'
*/
accountsPost.post = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: accountsPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::account
* @see app/Http/Controllers/Organisation/OrganisationController.php:165
* @route '/organisation/{organisation}/accounts/{account}'
*/
export const account = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(args, options),
    method: 'get',
})

account.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/accounts/{account}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::account
* @see app/Http/Controllers/Organisation/OrganisationController.php:165
* @route '/organisation/{organisation}/accounts/{account}'
*/
account.url = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            account: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        account: typeof args.account === 'object'
        ? args.account.id
        : args.account,
    }

    return account.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{account}', parsedArgs.account.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::account
* @see app/Http/Controllers/Organisation/OrganisationController.php:165
* @route '/organisation/{organisation}/accounts/{account}'
*/
account.get = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: account.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::account
* @see app/Http/Controllers/Organisation/OrganisationController.php:165
* @route '/organisation/{organisation}/accounts/{account}'
*/
account.head = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: account.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accountEditPost
* @see app/Http/Controllers/Organisation/OrganisationController.php:185
* @route '/organisation/{organisation}/accounts/{account}'
*/
export const accountEditPost = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: accountEditPost.url(args, options),
    method: 'post',
})

accountEditPost.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/accounts/{account}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accountEditPost
* @see app/Http/Controllers/Organisation/OrganisationController.php:185
* @route '/organisation/{organisation}/accounts/{account}'
*/
accountEditPost.url = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            account: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        account: typeof args.account === 'object'
        ? args.account.id
        : args.account,
    }

    return accountEditPost.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{account}', parsedArgs.account.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accountEditPost
* @see app/Http/Controllers/Organisation/OrganisationController.php:185
* @route '/organisation/{organisation}/accounts/{account}'
*/
accountEditPost.post = (args: { organisation: number | { id: number }, account: number | { id: number } } | [organisation: number | { id: number }, account: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: accountEditPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::cancelInvite
* @see app/Http/Controllers/Organisation/OrganisationController.php:215
* @route '/organisation/{organisation}/invite/{inviteId}/cancel'
*/
export const cancelInvite = (args: { organisation: number | { id: number }, inviteId: string | number } | [organisation: number | { id: number }, inviteId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cancelInvite.url(args, options),
    method: 'get',
})

cancelInvite.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/invite/{inviteId}/cancel',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::cancelInvite
* @see app/Http/Controllers/Organisation/OrganisationController.php:215
* @route '/organisation/{organisation}/invite/{inviteId}/cancel'
*/
cancelInvite.url = (args: { organisation: number | { id: number }, inviteId: string | number } | [organisation: number | { id: number }, inviteId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            inviteId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        inviteId: args.inviteId,
    }

    return cancelInvite.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{inviteId}', parsedArgs.inviteId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::cancelInvite
* @see app/Http/Controllers/Organisation/OrganisationController.php:215
* @route '/organisation/{organisation}/invite/{inviteId}/cancel'
*/
cancelInvite.get = (args: { organisation: number | { id: number }, inviteId: string | number } | [organisation: number | { id: number }, inviteId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cancelInvite.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::cancelInvite
* @see app/Http/Controllers/Organisation/OrganisationController.php:215
* @route '/organisation/{organisation}/invite/{inviteId}/cancel'
*/
cancelInvite.head = (args: { organisation: number | { id: number }, inviteId: string | number } | [organisation: number | { id: number }, inviteId: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: cancelInvite.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accountRemove
* @see app/Http/Controllers/Organisation/OrganisationController.php:202
* @route '/organisation/{organisation}/accounts/remove'
*/
export const accountRemove = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: accountRemove.url(args, options),
    method: 'delete',
})

accountRemove.definition = {
    methods: ["delete"],
    url: '/organisation/{organisation}/accounts/remove',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accountRemove
* @see app/Http/Controllers/Organisation/OrganisationController.php:202
* @route '/organisation/{organisation}/accounts/remove'
*/
accountRemove.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return accountRemove.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::accountRemove
* @see app/Http/Controllers/Organisation/OrganisationController.php:202
* @route '/organisation/{organisation}/accounts/remove'
*/
accountRemove.delete = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: accountRemove.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::infractions
* @see app/Http/Controllers/Organisation/OrganisationController.php:295
* @route '/organisation/{organisation}/infractions'
*/
export const infractions = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: infractions.url(args, options),
    method: 'get',
})

infractions.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/infractions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::infractions
* @see app/Http/Controllers/Organisation/OrganisationController.php:295
* @route '/organisation/{organisation}/infractions'
*/
infractions.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return infractions.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::infractions
* @see app/Http/Controllers/Organisation/OrganisationController.php:295
* @route '/organisation/{organisation}/infractions'
*/
infractions.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: infractions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::infractions
* @see app/Http/Controllers/Organisation/OrganisationController.php:295
* @route '/organisation/{organisation}/infractions'
*/
infractions.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: infractions.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:391
* @route '/organisation/{organisation}/infractions/create'
*/
export const createInfraction = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createInfraction.url(args, options),
    method: 'post',
})

createInfraction.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/infractions/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:391
* @route '/organisation/{organisation}/infractions/create'
*/
createInfraction.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return createInfraction.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:391
* @route '/organisation/{organisation}/infractions/create'
*/
createInfraction.post = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createInfraction.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::getInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:304
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
export const getInfraction = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getInfraction.url(args, options),
    method: 'get',
})

getInfraction.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/infractions/{type}/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::getInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:304
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
getInfraction.url = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            type: args[1],
            id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        type: args.type,
        id: args.id,
    }

    return getInfraction.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{type}', parsedArgs.type.toString())
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::getInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:304
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
getInfraction.get = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getInfraction.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::getInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:304
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
getInfraction.head = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getInfraction.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::updateInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:332
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
export const updateInfraction = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateInfraction.url(args, options),
    method: 'post',
})

updateInfraction.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/infractions/{type}/{id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::updateInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:332
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
updateInfraction.url = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            type: args[1],
            id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        type: args.type,
        id: args.id,
    }

    return updateInfraction.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{type}', parsedArgs.type.toString())
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::updateInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:332
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
updateInfraction.post = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateInfraction.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:446
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
export const deleteInfraction = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteInfraction.url(args, options),
    method: 'delete',
})

deleteInfraction.definition = {
    methods: ["delete"],
    url: '/organisation/{organisation}/infractions/{type}/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:446
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
deleteInfraction.url = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            type: args[1],
            id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        type: args.type,
        id: args.id,
    }

    return deleteInfraction.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{type}', parsedArgs.type.toString())
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteInfraction
* @see app/Http/Controllers/Organisation/OrganisationController.php:446
* @route '/organisation/{organisation}/infractions/{type}/{id}'
*/
deleteInfraction.delete = (args: { organisation: number | { id: number }, type: string | number, id: string | number } | [organisation: number | { id: number }, type: string | number, id: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteInfraction.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::scoringSettings
* @see app/Http/Controllers/Organisation/OrganisationController.php:229
* @route '/organisation/{organisation}/scoring'
*/
export const scoringSettings = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scoringSettings.url(args, options),
    method: 'get',
})

scoringSettings.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/scoring',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::scoringSettings
* @see app/Http/Controllers/Organisation/OrganisationController.php:229
* @route '/organisation/{organisation}/scoring'
*/
scoringSettings.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return scoringSettings.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::scoringSettings
* @see app/Http/Controllers/Organisation/OrganisationController.php:229
* @route '/organisation/{organisation}/scoring'
*/
scoringSettings.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scoringSettings.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::scoringSettings
* @see app/Http/Controllers/Organisation/OrganisationController.php:229
* @route '/organisation/{organisation}/scoring'
*/
scoringSettings.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: scoringSettings.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createResultSchemaTemplatePost
* @see app/Http/Controllers/Organisation/OrganisationController.php:478
* @route '/organisation/{organisation}/scoring/result-schemas/create'
*/
export const createResultSchemaTemplatePost = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createResultSchemaTemplatePost.url(args, options),
    method: 'post',
})

createResultSchemaTemplatePost.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/scoring/result-schemas/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createResultSchemaTemplatePost
* @see app/Http/Controllers/Organisation/OrganisationController.php:478
* @route '/organisation/{organisation}/scoring/result-schemas/create'
*/
createResultSchemaTemplatePost.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return createResultSchemaTemplatePost.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createResultSchemaTemplatePost
* @see app/Http/Controllers/Organisation/OrganisationController.php:478
* @route '/organisation/{organisation}/scoring/result-schemas/create'
*/
createResultSchemaTemplatePost.post = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createResultSchemaTemplatePost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createResultSchemaTemplate
* @see app/Http/Controllers/Organisation/OrganisationController.php:473
* @route '/organisation/{organisation}/scoring/result-schemas/create'
*/
export const createResultSchemaTemplate = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createResultSchemaTemplate.url(args, options),
    method: 'get',
})

createResultSchemaTemplate.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/scoring/result-schemas/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createResultSchemaTemplate
* @see app/Http/Controllers/Organisation/OrganisationController.php:473
* @route '/organisation/{organisation}/scoring/result-schemas/create'
*/
createResultSchemaTemplate.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return createResultSchemaTemplate.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createResultSchemaTemplate
* @see app/Http/Controllers/Organisation/OrganisationController.php:473
* @route '/organisation/{organisation}/scoring/result-schemas/create'
*/
createResultSchemaTemplate.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createResultSchemaTemplate.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createResultSchemaTemplate
* @see app/Http/Controllers/Organisation/OrganisationController.php:473
* @route '/organisation/{organisation}/scoring/result-schemas/create'
*/
createResultSchemaTemplate.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: createResultSchemaTemplate.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editResultSchemaTemplate
* @see app/Http/Controllers/Organisation/OrganisationController.php:490
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}/edit'
*/
export const editResultSchemaTemplate = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editResultSchemaTemplate.url(args, options),
    method: 'get',
})

editResultSchemaTemplate.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/scoring/result-schemas/{schema}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editResultSchemaTemplate
* @see app/Http/Controllers/Organisation/OrganisationController.php:490
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}/edit'
*/
editResultSchemaTemplate.url = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return editResultSchemaTemplate.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editResultSchemaTemplate
* @see app/Http/Controllers/Organisation/OrganisationController.php:490
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}/edit'
*/
editResultSchemaTemplate.get = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editResultSchemaTemplate.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editResultSchemaTemplate
* @see app/Http/Controllers/Organisation/OrganisationController.php:490
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}/edit'
*/
editResultSchemaTemplate.head = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: editResultSchemaTemplate.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editResultSchemaTemplatePost
* @see app/Http/Controllers/Organisation/OrganisationController.php:503
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}/edit'
*/
export const editResultSchemaTemplatePost = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editResultSchemaTemplatePost.url(args, options),
    method: 'post',
})

editResultSchemaTemplatePost.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/scoring/result-schemas/{schema}/edit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editResultSchemaTemplatePost
* @see app/Http/Controllers/Organisation/OrganisationController.php:503
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}/edit'
*/
editResultSchemaTemplatePost.url = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return editResultSchemaTemplatePost.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editResultSchemaTemplatePost
* @see app/Http/Controllers/Organisation/OrganisationController.php:503
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}/edit'
*/
editResultSchemaTemplatePost.post = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editResultSchemaTemplatePost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteResultSchemaTemplate
* @see app/Http/Controllers/Organisation/OrganisationController.php:0
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}'
*/
export const deleteResultSchemaTemplate = (args: { organisation: string | number, schema: string | number } | [organisation: string | number, schema: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteResultSchemaTemplate.url(args, options),
    method: 'delete',
})

deleteResultSchemaTemplate.definition = {
    methods: ["delete"],
    url: '/organisation/{organisation}/scoring/result-schemas/{schema}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteResultSchemaTemplate
* @see app/Http/Controllers/Organisation/OrganisationController.php:0
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}'
*/
deleteResultSchemaTemplate.url = (args: { organisation: string | number, schema: string | number } | [organisation: string | number, schema: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: args.organisation,
        schema: args.schema,
    }

    return deleteResultSchemaTemplate.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteResultSchemaTemplate
* @see app/Http/Controllers/Organisation/OrganisationController.php:0
* @route '/organisation/{organisation}/scoring/result-schemas/{schema}'
*/
deleteResultSchemaTemplate.delete = (args: { organisation: string | number, schema: string | number } | [organisation: string | number, schema: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteResultSchemaTemplate.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createScoringSchemaPost
* @see app/Http/Controllers/Organisation/OrganisationController.php:241
* @route '/organisation/{organisation}/scoring/create'
*/
export const createScoringSchemaPost = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createScoringSchemaPost.url(args, options),
    method: 'post',
})

createScoringSchemaPost.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/scoring/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createScoringSchemaPost
* @see app/Http/Controllers/Organisation/OrganisationController.php:241
* @route '/organisation/{organisation}/scoring/create'
*/
createScoringSchemaPost.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return createScoringSchemaPost.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createScoringSchemaPost
* @see app/Http/Controllers/Organisation/OrganisationController.php:241
* @route '/organisation/{organisation}/scoring/create'
*/
createScoringSchemaPost.post = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createScoringSchemaPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createScoringSchema
* @see app/Http/Controllers/Organisation/OrganisationController.php:236
* @route '/organisation/{organisation}/scoring/create'
*/
export const createScoringSchema = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createScoringSchema.url(args, options),
    method: 'get',
})

createScoringSchema.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/scoring/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createScoringSchema
* @see app/Http/Controllers/Organisation/OrganisationController.php:236
* @route '/organisation/{organisation}/scoring/create'
*/
createScoringSchema.url = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { organisation: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { organisation: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
    }

    return createScoringSchema.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createScoringSchema
* @see app/Http/Controllers/Organisation/OrganisationController.php:236
* @route '/organisation/{organisation}/scoring/create'
*/
createScoringSchema.get = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createScoringSchema.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::createScoringSchema
* @see app/Http/Controllers/Organisation/OrganisationController.php:236
* @route '/organisation/{organisation}/scoring/create'
*/
createScoringSchema.head = (args: { organisation: number | { id: number } } | [organisation: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: createScoringSchema.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editScoringSchema
* @see app/Http/Controllers/Organisation/OrganisationController.php:255
* @route '/organisation/{organisation}/scoring/{schema}/edit'
*/
export const editScoringSchema = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editScoringSchema.url(args, options),
    method: 'get',
})

editScoringSchema.definition = {
    methods: ["get","head"],
    url: '/organisation/{organisation}/scoring/{schema}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editScoringSchema
* @see app/Http/Controllers/Organisation/OrganisationController.php:255
* @route '/organisation/{organisation}/scoring/{schema}/edit'
*/
editScoringSchema.url = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return editScoringSchema.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editScoringSchema
* @see app/Http/Controllers/Organisation/OrganisationController.php:255
* @route '/organisation/{organisation}/scoring/{schema}/edit'
*/
editScoringSchema.get = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editScoringSchema.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editScoringSchema
* @see app/Http/Controllers/Organisation/OrganisationController.php:255
* @route '/organisation/{organisation}/scoring/{schema}/edit'
*/
editScoringSchema.head = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: editScoringSchema.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editScoringSchemaPost
* @see app/Http/Controllers/Organisation/OrganisationController.php:268
* @route '/organisation/{organisation}/scoring/{schema}/edit'
*/
export const editScoringSchemaPost = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editScoringSchemaPost.url(args, options),
    method: 'post',
})

editScoringSchemaPost.definition = {
    methods: ["post"],
    url: '/organisation/{organisation}/scoring/{schema}/edit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editScoringSchemaPost
* @see app/Http/Controllers/Organisation/OrganisationController.php:268
* @route '/organisation/{organisation}/scoring/{schema}/edit'
*/
editScoringSchemaPost.url = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return editScoringSchemaPost.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::editScoringSchemaPost
* @see app/Http/Controllers/Organisation/OrganisationController.php:268
* @route '/organisation/{organisation}/scoring/{schema}/edit'
*/
editScoringSchemaPost.post = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: editScoringSchemaPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteScoringSchema
* @see app/Http/Controllers/Organisation/OrganisationController.php:281
* @route '/organisation/{organisation}/scoring/{schema}'
*/
export const deleteScoringSchema = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteScoringSchema.url(args, options),
    method: 'delete',
})

deleteScoringSchema.definition = {
    methods: ["delete"],
    url: '/organisation/{organisation}/scoring/{schema}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteScoringSchema
* @see app/Http/Controllers/Organisation/OrganisationController.php:281
* @route '/organisation/{organisation}/scoring/{schema}'
*/
deleteScoringSchema.url = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            organisation: args[0],
            schema: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        organisation: typeof args.organisation === 'object'
        ? args.organisation.id
        : args.organisation,
        schema: typeof args.schema === 'object'
        ? args.schema.id
        : args.schema,
    }

    return deleteScoringSchema.definition.url
            .replace('{organisation}', parsedArgs.organisation.toString())
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Organisation\OrganisationController::deleteScoringSchema
* @see app/Http/Controllers/Organisation/OrganisationController.php:281
* @route '/organisation/{organisation}/scoring/{schema}'
*/
deleteScoringSchema.delete = (args: { organisation: number | { id: number }, schema: number | { id: number } } | [organisation: number | { id: number }, schema: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteScoringSchema.url(args, options),
    method: 'delete',
})

const OrganisationController = { index, create, store, show, edit, update, destroy, accounts, accountsPost, account, accountEditPost, cancelInvite, accountRemove, infractions, createInfraction, getInfraction, updateInfraction, deleteInfraction, scoringSettings, createResultSchemaTemplatePost, createResultSchemaTemplate, editResultSchemaTemplate, editResultSchemaTemplatePost, deleteResultSchemaTemplate, createScoringSchemaPost, createScoringSchema, editScoringSchema, editScoringSchemaPost, deleteScoringSchema }

export default OrganisationController