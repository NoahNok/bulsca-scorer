import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\AdminController::index
* @see app/Http/Controllers/AdminController.php:19
* @route '/admin'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::index
* @see app/Http/Controllers/AdminController.php:19
* @route '/admin'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::index
* @see app/Http/Controllers/AdminController.php:19
* @route '/admin'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::index
* @see app/Http/Controllers/AdminController.php:19
* @route '/admin'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::createComp
* @see app/Http/Controllers/AdminController.php:24
* @route '/admin/competition/create'
*/
export const createComp = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createComp.url(options),
    method: 'get',
})

createComp.definition = {
    methods: ["get","head"],
    url: '/admin/competition/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::createComp
* @see app/Http/Controllers/AdminController.php:24
* @route '/admin/competition/create'
*/
createComp.url = (options?: RouteQueryOptions) => {
    return createComp.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::createComp
* @see app/Http/Controllers/AdminController.php:24
* @route '/admin/competition/create'
*/
createComp.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: createComp.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::createComp
* @see app/Http/Controllers/AdminController.php:24
* @route '/admin/competition/create'
*/
createComp.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: createComp.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::viewComp
* @see app/Http/Controllers/AdminController.php:81
* @route '/admin/competition/{comp}'
*/
export const viewComp = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewComp.url(args, options),
    method: 'get',
})

viewComp.definition = {
    methods: ["get","head"],
    url: '/admin/competition/{comp}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::viewComp
* @see app/Http/Controllers/AdminController.php:81
* @route '/admin/competition/{comp}'
*/
viewComp.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { comp: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
    }

    return viewComp.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::viewComp
* @see app/Http/Controllers/AdminController.php:81
* @route '/admin/competition/{comp}'
*/
viewComp.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: viewComp.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::viewComp
* @see app/Http/Controllers/AdminController.php:81
* @route '/admin/competition/{comp}'
*/
viewComp.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: viewComp.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::createCompPost
* @see app/Http/Controllers/AdminController.php:30
* @route '/admin/competition/create'
*/
export const createCompPost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createCompPost.url(options),
    method: 'post',
})

createCompPost.definition = {
    methods: ["post"],
    url: '/admin/competition/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::createCompPost
* @see app/Http/Controllers/AdminController.php:30
* @route '/admin/competition/create'
*/
createCompPost.url = (options?: RouteQueryOptions) => {
    return createCompPost.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::createCompPost
* @see app/Http/Controllers/AdminController.php:30
* @route '/admin/competition/create'
*/
createCompPost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createCompPost.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminController::updateCompPost
* @see app/Http/Controllers/AdminController.php:56
* @route '/admin/competition/{comp}/update'
*/
export const updateCompPost = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateCompPost.url(args, options),
    method: 'post',
})

updateCompPost.definition = {
    methods: ["post"],
    url: '/admin/competition/{comp}/update',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::updateCompPost
* @see app/Http/Controllers/AdminController.php:56
* @route '/admin/competition/{comp}/update'
*/
updateCompPost.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { comp: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
    }

    return updateCompPost.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::updateCompPost
* @see app/Http/Controllers/AdminController.php:56
* @route '/admin/competition/{comp}/update'
*/
updateCompPost.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateCompPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminController::updateCompUserPassword
* @see app/Http/Controllers/AdminController.php:86
* @route '/admin/competition/{comp}/updateUser'
*/
export const updateCompUserPassword = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateCompUserPassword.url(args, options),
    method: 'post',
})

updateCompUserPassword.definition = {
    methods: ["post"],
    url: '/admin/competition/{comp}/updateUser',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::updateCompUserPassword
* @see app/Http/Controllers/AdminController.php:86
* @route '/admin/competition/{comp}/updateUser'
*/
updateCompUserPassword.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { comp: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
    }

    return updateCompUserPassword.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::updateCompUserPassword
* @see app/Http/Controllers/AdminController.php:86
* @route '/admin/competition/{comp}/updateUser'
*/
updateCompUserPassword.post = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateCompUserPassword.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminController::records
* @see app/Http/Controllers/AdminController.php:102
* @route '/admin/records'
*/
export const records = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: records.url(options),
    method: 'get',
})

records.definition = {
    methods: ["get","head"],
    url: '/admin/records',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::records
* @see app/Http/Controllers/AdminController.php:102
* @route '/admin/records'
*/
records.url = (options?: RouteQueryOptions) => {
    return records.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::records
* @see app/Http/Controllers/AdminController.php:102
* @route '/admin/records'
*/
records.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: records.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::records
* @see app/Http/Controllers/AdminController.php:102
* @route '/admin/records'
*/
records.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: records.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::updateRecords
* @see app/Http/Controllers/AdminController.php:108
* @route '/admin/records'
*/
export const updateRecords = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateRecords.url(options),
    method: 'post',
})

updateRecords.definition = {
    methods: ["post"],
    url: '/admin/records',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::updateRecords
* @see app/Http/Controllers/AdminController.php:108
* @route '/admin/records'
*/
updateRecords.url = (options?: RouteQueryOptions) => {
    return updateRecords.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::updateRecords
* @see app/Http/Controllers/AdminController.php:108
* @route '/admin/records'
*/
updateRecords.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateRecords.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminController::seasons
* @see app/Http/Controllers/AdminController.php:148
* @route '/admin/seasons'
*/
export const seasons = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: seasons.url(options),
    method: 'get',
})

seasons.definition = {
    methods: ["get","head"],
    url: '/admin/seasons',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::seasons
* @see app/Http/Controllers/AdminController.php:148
* @route '/admin/seasons'
*/
seasons.url = (options?: RouteQueryOptions) => {
    return seasons.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::seasons
* @see app/Http/Controllers/AdminController.php:148
* @route '/admin/seasons'
*/
seasons.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: seasons.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::seasons
* @see app/Http/Controllers/AdminController.php:148
* @route '/admin/seasons'
*/
seasons.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: seasons.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::seasonCreate
* @see app/Http/Controllers/AdminController.php:153
* @route '/admin/season/create'
*/
export const seasonCreate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: seasonCreate.url(options),
    method: 'get',
})

seasonCreate.definition = {
    methods: ["get","head"],
    url: '/admin/season/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::seasonCreate
* @see app/Http/Controllers/AdminController.php:153
* @route '/admin/season/create'
*/
seasonCreate.url = (options?: RouteQueryOptions) => {
    return seasonCreate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::seasonCreate
* @see app/Http/Controllers/AdminController.php:153
* @route '/admin/season/create'
*/
seasonCreate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: seasonCreate.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::seasonCreate
* @see app/Http/Controllers/AdminController.php:153
* @route '/admin/season/create'
*/
seasonCreate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: seasonCreate.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::seasonCreatePost
* @see app/Http/Controllers/AdminController.php:159
* @route '/admin/season/create'
*/
export const seasonCreatePost = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: seasonCreatePost.url(options),
    method: 'post',
})

seasonCreatePost.definition = {
    methods: ["post"],
    url: '/admin/season/create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::seasonCreatePost
* @see app/Http/Controllers/AdminController.php:159
* @route '/admin/season/create'
*/
seasonCreatePost.url = (options?: RouteQueryOptions) => {
    return seasonCreatePost.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::seasonCreatePost
* @see app/Http/Controllers/AdminController.php:159
* @route '/admin/season/create'
*/
seasonCreatePost.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: seasonCreatePost.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminController::seasonEdit
* @see app/Http/Controllers/AdminController.php:172
* @route '/admin/season/edit/{season}'
*/
export const seasonEdit = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: seasonEdit.url(args, options),
    method: 'get',
})

seasonEdit.definition = {
    methods: ["get","head"],
    url: '/admin/season/edit/{season}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\AdminController::seasonEdit
* @see app/Http/Controllers/AdminController.php:172
* @route '/admin/season/edit/{season}'
*/
seasonEdit.url = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { season: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { season: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            season: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        season: typeof args.season === 'object'
        ? args.season.id
        : args.season,
    }

    return seasonEdit.definition.url
            .replace('{season}', parsedArgs.season.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::seasonEdit
* @see app/Http/Controllers/AdminController.php:172
* @route '/admin/season/edit/{season}'
*/
seasonEdit.get = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: seasonEdit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\AdminController::seasonEdit
* @see app/Http/Controllers/AdminController.php:172
* @route '/admin/season/edit/{season}'
*/
seasonEdit.head = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: seasonEdit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\AdminController::seasonEditPost
* @see app/Http/Controllers/AdminController.php:177
* @route '/admin/season/edit/{season}'
*/
export const seasonEditPost = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: seasonEditPost.url(args, options),
    method: 'post',
})

seasonEditPost.definition = {
    methods: ["post"],
    url: '/admin/season/edit/{season}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\AdminController::seasonEditPost
* @see app/Http/Controllers/AdminController.php:177
* @route '/admin/season/edit/{season}'
*/
seasonEditPost.url = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { season: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { season: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            season: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        season: typeof args.season === 'object'
        ? args.season.id
        : args.season,
    }

    return seasonEditPost.definition.url
            .replace('{season}', parsedArgs.season.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::seasonEditPost
* @see app/Http/Controllers/AdminController.php:177
* @route '/admin/season/edit/{season}'
*/
seasonEditPost.post = (args: { season: number | { id: number } } | [season: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: seasonEditPost.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\AdminController::deleteCompPost
* @see app/Http/Controllers/AdminController.php:132
* @route '/admin/competition/{comp}/delete'
*/
export const deleteCompPost = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCompPost.url(args, options),
    method: 'delete',
})

deleteCompPost.definition = {
    methods: ["delete"],
    url: '/admin/competition/{comp}/delete',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\AdminController::deleteCompPost
* @see app/Http/Controllers/AdminController.php:132
* @route '/admin/competition/{comp}/delete'
*/
deleteCompPost.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { comp: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { comp: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            comp: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
    }

    return deleteCompPost.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\AdminController::deleteCompPost
* @see app/Http/Controllers/AdminController.php:132
* @route '/admin/competition/{comp}/delete'
*/
deleteCompPost.delete = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCompPost.url(args, options),
    method: 'delete',
})

const AdminController = { index, createComp, viewComp, createCompPost, updateCompPost, updateCompUserPassword, records, updateRecords, seasons, seasonCreate, seasonCreatePost, seasonEdit, seasonEditPost, deleteCompPost }

export default AdminController