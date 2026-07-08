import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import editor774148 from './editor'
/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::clone
* @see app/Http/Controllers/WhatIf/WhatIfController.php:41
* @route '//whatif.localhost/cas'
*/
export const clone = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: clone.url(options),
    method: 'post',
})

clone.definition = {
    methods: ["post"],
    url: '//whatif.localhost/cas',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::clone
* @see app/Http/Controllers/WhatIf/WhatIfController.php:41
* @route '//whatif.localhost/cas'
*/
clone.url = (options?: RouteQueryOptions) => {
    return clone.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::clone
* @see app/Http/Controllers/WhatIf/WhatIfController.php:41
* @route '//whatif.localhost/cas'
*/
clone.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: clone.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::resume
* @see app/Http/Controllers/WhatIf/WhatIfController.php:90
* @route '//whatif.localhost/resume'
*/
export const resume = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resume.url(options),
    method: 'post',
})

resume.definition = {
    methods: ["post"],
    url: '//whatif.localhost/resume',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::resume
* @see app/Http/Controllers/WhatIf/WhatIfController.php:90
* @route '//whatif.localhost/resume'
*/
resume.url = (options?: RouteQueryOptions) => {
    return resume.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::resume
* @see app/Http/Controllers/WhatIf/WhatIfController.php:90
* @route '//whatif.localhost/resume'
*/
resume.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: resume.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::admin
* @see app/Http/Controllers/WhatIf/WhatIfController.php:456
* @route '//whatif.localhost/admin'
*/
export const admin = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: admin.url(options),
    method: 'get',
})

admin.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::admin
* @see app/Http/Controllers/WhatIf/WhatIfController.php:456
* @route '//whatif.localhost/admin'
*/
admin.url = (options?: RouteQueryOptions) => {
    return admin.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::admin
* @see app/Http/Controllers/WhatIf/WhatIfController.php:456
* @route '//whatif.localhost/admin'
*/
admin.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: admin.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::admin
* @see app/Http/Controllers/WhatIf/WhatIfController.php:456
* @route '//whatif.localhost/admin'
*/
admin.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: admin.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::editor
* @see app/Http/Controllers/WhatIf/WhatIfController.php:105
* @route '//whatif.localhost/editor'
*/
export const editor = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editor.url(options),
    method: 'get',
})

editor.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::editor
* @see app/Http/Controllers/WhatIf/WhatIfController.php:105
* @route '//whatif.localhost/editor'
*/
editor.url = (options?: RouteQueryOptions) => {
    return editor.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::editor
* @see app/Http/Controllers/WhatIf/WhatIfController.php:105
* @route '//whatif.localhost/editor'
*/
editor.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editor.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::editor
* @see app/Http/Controllers/WhatIf/WhatIfController.php:105
* @route '//whatif.localhost/editor'
*/
editor.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: editor.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::userc
* @see app/Http/Controllers/WhatIf/WhatIfController.php:150
* @route '//whatif.localhost/editor/userc'
*/
export const userc = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: userc.url(options),
    method: 'post',
})

userc.definition = {
    methods: ["post"],
    url: '//whatif.localhost/editor/userc',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::userc
* @see app/Http/Controllers/WhatIf/WhatIfController.php:150
* @route '//whatif.localhost/editor/userc'
*/
userc.url = (options?: RouteQueryOptions) => {
    return userc.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::userc
* @see app/Http/Controllers/WhatIf/WhatIfController.php:150
* @route '//whatif.localhost/editor/userc'
*/
userc.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: userc.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::uspeed
* @see app/Http/Controllers/WhatIf/WhatIfController.php:175
* @route '//whatif.localhost/editor/uspeed'
*/
export const uspeed = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: uspeed.url(options),
    method: 'post',
})

uspeed.definition = {
    methods: ["post"],
    url: '//whatif.localhost/editor/uspeed',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::uspeed
* @see app/Http/Controllers/WhatIf/WhatIfController.php:175
* @route '//whatif.localhost/editor/uspeed'
*/
uspeed.url = (options?: RouteQueryOptions) => {
    return uspeed.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::uspeed
* @see app/Http/Controllers/WhatIf/WhatIfController.php:175
* @route '//whatif.localhost/editor/uspeed'
*/
uspeed.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: uspeed.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::switchMethod
* @see app/Http/Controllers/WhatIf/WhatIfController.php:282
* @route '//whatif.localhost/editor/switch/{comp}'
*/
export const switchMethod = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: switchMethod.url(args, options),
    method: 'get',
})

switchMethod.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor/switch/{comp}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::switchMethod
* @see app/Http/Controllers/WhatIf/WhatIfController.php:282
* @route '//whatif.localhost/editor/switch/{comp}'
*/
switchMethod.url = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return switchMethod.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::switchMethod
* @see app/Http/Controllers/WhatIf/WhatIfController.php:282
* @route '//whatif.localhost/editor/switch/{comp}'
*/
switchMethod.get = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: switchMethod.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::switchMethod
* @see app/Http/Controllers/WhatIf/WhatIfController.php:282
* @route '//whatif.localhost/editor/switch/{comp}'
*/
switchMethod.head = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: switchMethod.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::internalCas
* @see app/Http/Controllers/WhatIf/WhatIfController.php:310
* @route '//whatif.localhost/editor/internalCas'
*/
export const internalCas = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: internalCas.url(options),
    method: 'post',
})

internalCas.definition = {
    methods: ["post"],
    url: '//whatif.localhost/editor/internalCas',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::internalCas
* @see app/Http/Controllers/WhatIf/WhatIfController.php:310
* @route '//whatif.localhost/editor/internalCas'
*/
internalCas.url = (options?: RouteQueryOptions) => {
    return internalCas.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::internalCas
* @see app/Http/Controllers/WhatIf/WhatIfController.php:310
* @route '//whatif.localhost/editor/internalCas'
*/
internalCas.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: internalCas.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::deleteMethod
* @see app/Http/Controllers/WhatIf/WhatIfController.php:351
* @route '//whatif.localhost/editor/delete'
*/
export const deleteMethod = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deleteMethod.url(options),
    method: 'post',
})

deleteMethod.definition = {
    methods: ["post"],
    url: '//whatif.localhost/editor/delete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::deleteMethod
* @see app/Http/Controllers/WhatIf/WhatIfController.php:351
* @route '//whatif.localhost/editor/delete'
*/
deleteMethod.url = (options?: RouteQueryOptions) => {
    return deleteMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::deleteMethod
* @see app/Http/Controllers/WhatIf/WhatIfController.php:351
* @route '//whatif.localhost/editor/delete'
*/
deleteMethod.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deleteMethod.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::reset
* @see app/Http/Controllers/WhatIf/WhatIfController.php:393
* @route '//whatif.localhost/editor/reset'
*/
export const reset = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reset.url(options),
    method: 'get',
})

reset.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor/reset',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::reset
* @see app/Http/Controllers/WhatIf/WhatIfController.php:393
* @route '//whatif.localhost/editor/reset'
*/
reset.url = (options?: RouteQueryOptions) => {
    return reset.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::reset
* @see app/Http/Controllers/WhatIf/WhatIfController.php:393
* @route '//whatif.localhost/editor/reset'
*/
reset.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reset.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::reset
* @see app/Http/Controllers/WhatIf/WhatIfController.php:393
* @route '//whatif.localhost/editor/reset'
*/
reset.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reset.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::select
* @see app/Http/Controllers/WhatIf/WhatIfController.php:420
* @route '//whatif.localhost/editor/select'
*/
export const select = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: select.url(options),
    method: 'get',
})

select.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor/select',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::select
* @see app/Http/Controllers/WhatIf/WhatIfController.php:420
* @route '//whatif.localhost/editor/select'
*/
select.url = (options?: RouteQueryOptions) => {
    return select.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::select
* @see app/Http/Controllers/WhatIf/WhatIfController.php:420
* @route '//whatif.localhost/editor/select'
*/
select.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: select.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::select
* @see app/Http/Controllers/WhatIf/WhatIfController.php:420
* @route '//whatif.localhost/editor/select'
*/
select.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: select.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::logout
* @see app/Http/Controllers/WhatIf/WhatIfController.php:426
* @route '//whatif.localhost/editor/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logout.url(options),
    method: 'get',
})

logout.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor/logout',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::logout
* @see app/Http/Controllers/WhatIf/WhatIfController.php:426
* @route '//whatif.localhost/editor/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::logout
* @see app/Http/Controllers/WhatIf/WhatIfController.php:426
* @route '//whatif.localhost/editor/logout'
*/
logout.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: logout.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::logout
* @see app/Http/Controllers/WhatIf/WhatIfController.php:426
* @route '//whatif.localhost/editor/logout'
*/
logout.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: logout.url(options),
    method: 'head',
})

const whatif = {
    clone: Object.assign(clone, clone),
    resume: Object.assign(resume, resume),
    admin: Object.assign(admin, admin),
    editor: Object.assign(editor, editor774148),
    userc: Object.assign(userc, userc),
    uspeed: Object.assign(uspeed, uspeed),
    switch: Object.assign(switchMethod, switchMethod),
    internalCas: Object.assign(internalCas, internalCas),
    delete: Object.assign(deleteMethod, deleteMethod),
    reset: Object.assign(reset, reset),
    select: Object.assign(select, select),
    logout: Object.assign(logout, logout),
}

export default whatif