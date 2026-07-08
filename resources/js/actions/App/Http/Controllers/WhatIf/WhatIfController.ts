import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::index
* @see app/Http/Controllers/WhatIf/WhatIfController.php:30
* @route '//whatif.localhost'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::index
* @see app/Http/Controllers/WhatIf/WhatIfController.php:30
* @route '//whatif.localhost'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::index
* @see app/Http/Controllers/WhatIf/WhatIfController.php:30
* @route '//whatif.localhost'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::index
* @see app/Http/Controllers/WhatIf/WhatIfController.php:30
* @route '//whatif.localhost'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::cloneAndStart
* @see app/Http/Controllers/WhatIf/WhatIfController.php:41
* @route '//whatif.localhost/cas'
*/
export const cloneAndStart = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cloneAndStart.url(options),
    method: 'post',
})

cloneAndStart.definition = {
    methods: ["post"],
    url: '//whatif.localhost/cas',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::cloneAndStart
* @see app/Http/Controllers/WhatIf/WhatIfController.php:41
* @route '//whatif.localhost/cas'
*/
cloneAndStart.url = (options?: RouteQueryOptions) => {
    return cloneAndStart.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::cloneAndStart
* @see app/Http/Controllers/WhatIf/WhatIfController.php:41
* @route '//whatif.localhost/cas'
*/
cloneAndStart.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cloneAndStart.url(options),
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
* @see \App\Http\Controllers\WhatIf\WhatIfController::adminIndex
* @see app/Http/Controllers/WhatIf/WhatIfController.php:456
* @route '//whatif.localhost/admin'
*/
export const adminIndex = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: adminIndex.url(options),
    method: 'get',
})

adminIndex.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/admin',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::adminIndex
* @see app/Http/Controllers/WhatIf/WhatIfController.php:456
* @route '//whatif.localhost/admin'
*/
adminIndex.url = (options?: RouteQueryOptions) => {
    return adminIndex.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::adminIndex
* @see app/Http/Controllers/WhatIf/WhatIfController.php:456
* @route '//whatif.localhost/admin'
*/
adminIndex.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: adminIndex.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::adminIndex
* @see app/Http/Controllers/WhatIf/WhatIfController.php:456
* @route '//whatif.localhost/admin'
*/
adminIndex.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: adminIndex.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::editorIndex
* @see app/Http/Controllers/WhatIf/WhatIfController.php:105
* @route '//whatif.localhost/editor'
*/
export const editorIndex = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editorIndex.url(options),
    method: 'get',
})

editorIndex.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::editorIndex
* @see app/Http/Controllers/WhatIf/WhatIfController.php:105
* @route '//whatif.localhost/editor'
*/
editorIndex.url = (options?: RouteQueryOptions) => {
    return editorIndex.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::editorIndex
* @see app/Http/Controllers/WhatIf/WhatIfController.php:105
* @route '//whatif.localhost/editor'
*/
editorIndex.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editorIndex.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::editorIndex
* @see app/Http/Controllers/WhatIf/WhatIfController.php:105
* @route '//whatif.localhost/editor'
*/
editorIndex.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: editorIndex.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::getSpeedResults
* @see app/Http/Controllers/WhatIf/WhatIfController.php:249
* @route '//whatif.localhost/editor/results/speeds/{speed}'
*/
export const getSpeedResults = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSpeedResults.url(args, options),
    method: 'get',
})

getSpeedResults.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor/results/speeds/{speed}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::getSpeedResults
* @see app/Http/Controllers/WhatIf/WhatIfController.php:249
* @route '//whatif.localhost/editor/results/speeds/{speed}'
*/
getSpeedResults.url = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { speed: args }
    }

    if (Array.isArray(args)) {
        args = {
            speed: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        speed: args.speed,
    }

    return getSpeedResults.definition.url
            .replace('{speed}', parsedArgs.speed.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::getSpeedResults
* @see app/Http/Controllers/WhatIf/WhatIfController.php:249
* @route '//whatif.localhost/editor/results/speeds/{speed}'
*/
getSpeedResults.get = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSpeedResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::getSpeedResults
* @see app/Http/Controllers/WhatIf/WhatIfController.php:249
* @route '//whatif.localhost/editor/results/speeds/{speed}'
*/
getSpeedResults.head = (args: { speed: string | number } | [speed: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSpeedResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::getSercResults
* @see app/Http/Controllers/WhatIf/WhatIfController.php:266
* @route '//whatif.localhost/editor/results/sercs/{serc}'
*/
export const getSercResults = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSercResults.url(args, options),
    method: 'get',
})

getSercResults.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor/results/sercs/{serc}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::getSercResults
* @see app/Http/Controllers/WhatIf/WhatIfController.php:266
* @route '//whatif.localhost/editor/results/sercs/{serc}'
*/
getSercResults.url = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getSercResults.definition.url
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::getSercResults
* @see app/Http/Controllers/WhatIf/WhatIfController.php:266
* @route '//whatif.localhost/editor/results/sercs/{serc}'
*/
getSercResults.get = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSercResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::getSercResults
* @see app/Http/Controllers/WhatIf/WhatIfController.php:266
* @route '//whatif.localhost/editor/results/sercs/{serc}'
*/
getSercResults.head = (args: { serc: string | number } | [serc: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSercResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::editorResults
* @see app/Http/Controllers/WhatIf/WhatIfController.php:126
* @route '//whatif.localhost/editor/results/{schema}'
*/
export const editorResults = (args: { schema: string | number } | [schema: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editorResults.url(args, options),
    method: 'get',
})

editorResults.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor/results/{schema}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::editorResults
* @see app/Http/Controllers/WhatIf/WhatIfController.php:126
* @route '//whatif.localhost/editor/results/{schema}'
*/
editorResults.url = (args: { schema: string | number } | [schema: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { schema: args }
    }

    if (Array.isArray(args)) {
        args = {
            schema: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        schema: args.schema,
    }

    return editorResults.definition.url
            .replace('{schema}', parsedArgs.schema.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::editorResults
* @see app/Http/Controllers/WhatIf/WhatIfController.php:126
* @route '//whatif.localhost/editor/results/{schema}'
*/
editorResults.get = (args: { schema: string | number } | [schema: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: editorResults.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::editorResults
* @see app/Http/Controllers/WhatIf/WhatIfController.php:126
* @route '//whatif.localhost/editor/results/{schema}'
*/
editorResults.head = (args: { schema: string | number } | [schema: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: editorResults.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::updateSercResult
* @see app/Http/Controllers/WhatIf/WhatIfController.php:150
* @route '//whatif.localhost/editor/userc'
*/
export const updateSercResult = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateSercResult.url(options),
    method: 'post',
})

updateSercResult.definition = {
    methods: ["post"],
    url: '//whatif.localhost/editor/userc',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::updateSercResult
* @see app/Http/Controllers/WhatIf/WhatIfController.php:150
* @route '//whatif.localhost/editor/userc'
*/
updateSercResult.url = (options?: RouteQueryOptions) => {
    return updateSercResult.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::updateSercResult
* @see app/Http/Controllers/WhatIf/WhatIfController.php:150
* @route '//whatif.localhost/editor/userc'
*/
updateSercResult.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateSercResult.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::updateSpeedResult
* @see app/Http/Controllers/WhatIf/WhatIfController.php:175
* @route '//whatif.localhost/editor/uspeed'
*/
export const updateSpeedResult = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateSpeedResult.url(options),
    method: 'post',
})

updateSpeedResult.definition = {
    methods: ["post"],
    url: '//whatif.localhost/editor/uspeed',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::updateSpeedResult
* @see app/Http/Controllers/WhatIf/WhatIfController.php:175
* @route '//whatif.localhost/editor/uspeed'
*/
updateSpeedResult.url = (options?: RouteQueryOptions) => {
    return updateSpeedResult.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::updateSpeedResult
* @see app/Http/Controllers/WhatIf/WhatIfController.php:175
* @route '//whatif.localhost/editor/uspeed'
*/
updateSpeedResult.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateSpeedResult.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::switchOpenEditor
* @see app/Http/Controllers/WhatIf/WhatIfController.php:282
* @route '//whatif.localhost/editor/switch/{comp}'
*/
export const switchOpenEditor = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: switchOpenEditor.url(args, options),
    method: 'get',
})

switchOpenEditor.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor/switch/{comp}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::switchOpenEditor
* @see app/Http/Controllers/WhatIf/WhatIfController.php:282
* @route '//whatif.localhost/editor/switch/{comp}'
*/
switchOpenEditor.url = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return switchOpenEditor.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::switchOpenEditor
* @see app/Http/Controllers/WhatIf/WhatIfController.php:282
* @route '//whatif.localhost/editor/switch/{comp}'
*/
switchOpenEditor.get = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: switchOpenEditor.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::switchOpenEditor
* @see app/Http/Controllers/WhatIf/WhatIfController.php:282
* @route '//whatif.localhost/editor/switch/{comp}'
*/
switchOpenEditor.head = (args: { comp: string | number } | [comp: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: switchOpenEditor.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::loggedInCloneAndSwitch
* @see app/Http/Controllers/WhatIf/WhatIfController.php:310
* @route '//whatif.localhost/editor/internalCas'
*/
export const loggedInCloneAndSwitch = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: loggedInCloneAndSwitch.url(options),
    method: 'post',
})

loggedInCloneAndSwitch.definition = {
    methods: ["post"],
    url: '//whatif.localhost/editor/internalCas',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::loggedInCloneAndSwitch
* @see app/Http/Controllers/WhatIf/WhatIfController.php:310
* @route '//whatif.localhost/editor/internalCas'
*/
loggedInCloneAndSwitch.url = (options?: RouteQueryOptions) => {
    return loggedInCloneAndSwitch.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::loggedInCloneAndSwitch
* @see app/Http/Controllers/WhatIf/WhatIfController.php:310
* @route '//whatif.localhost/editor/internalCas'
*/
loggedInCloneAndSwitch.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: loggedInCloneAndSwitch.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::deleteEditor
* @see app/Http/Controllers/WhatIf/WhatIfController.php:351
* @route '//whatif.localhost/editor/delete'
*/
export const deleteEditor = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deleteEditor.url(options),
    method: 'post',
})

deleteEditor.definition = {
    methods: ["post"],
    url: '//whatif.localhost/editor/delete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::deleteEditor
* @see app/Http/Controllers/WhatIf/WhatIfController.php:351
* @route '//whatif.localhost/editor/delete'
*/
deleteEditor.url = (options?: RouteQueryOptions) => {
    return deleteEditor.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::deleteEditor
* @see app/Http/Controllers/WhatIf/WhatIfController.php:351
* @route '//whatif.localhost/editor/delete'
*/
deleteEditor.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: deleteEditor.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::resetCurrentCompetition
* @see app/Http/Controllers/WhatIf/WhatIfController.php:393
* @route '//whatif.localhost/editor/reset'
*/
export const resetCurrentCompetition = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resetCurrentCompetition.url(options),
    method: 'get',
})

resetCurrentCompetition.definition = {
    methods: ["get","head"],
    url: '//whatif.localhost/editor/reset',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::resetCurrentCompetition
* @see app/Http/Controllers/WhatIf/WhatIfController.php:393
* @route '//whatif.localhost/editor/reset'
*/
resetCurrentCompetition.url = (options?: RouteQueryOptions) => {
    return resetCurrentCompetition.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::resetCurrentCompetition
* @see app/Http/Controllers/WhatIf/WhatIfController.php:393
* @route '//whatif.localhost/editor/reset'
*/
resetCurrentCompetition.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: resetCurrentCompetition.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\WhatIf\WhatIfController::resetCurrentCompetition
* @see app/Http/Controllers/WhatIf/WhatIfController.php:393
* @route '//whatif.localhost/editor/reset'
*/
resetCurrentCompetition.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: resetCurrentCompetition.url(options),
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

const WhatIfController = { index, cloneAndStart, resume, adminIndex, editorIndex, getSpeedResults, getSercResults, editorResults, updateSercResult, updateSpeedResult, switchOpenEditor, loggedInCloneAndSwitch, deleteEditor, resetCurrentCompetition, select, logout }

export default WhatIfController