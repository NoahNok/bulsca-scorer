import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::next
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:178
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/next'
*/
export const next = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: next.url(args, options),
    method: 'get',
})

next.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/mark/next',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::next
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:178
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/next'
*/
next.url = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return next.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::next
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:178
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/next'
*/
next.get = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: next.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::next
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:178
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/next'
*/
next.head = (args: { competition: number | { id: number }, serc: number | { id: number } } | [competition: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: next.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::entity
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:223
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
export const entity = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: entity.url(args, options),
    method: 'get',
})

entity.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::entity
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:223
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
entity.url = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
            entity_id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
        entity_id: args.entity_id,
    }

    return entity.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::entity
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:223
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
entity.get = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: entity.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::entity
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:223
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
entity.head = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: entity.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::store
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:270
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
export const store = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::store
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:270
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
store.url = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
            entity_id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: typeof args.competition === 'object'
        ? args.competition.id
        : args.competition,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
        entity_id: args.entity_id,
    }

    return store.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::store
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:270
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/e/{entity_id}'
*/
store.post = (args: { competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number } | [competition: number | { id: number }, serc: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::notes
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:334
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/notes'
*/
export const notes = (args: { competition: string | number, serc: string | number } | [competition: string | number, serc: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notes.url(args, options),
    method: 'get',
})

notes.definition = {
    methods: ["get","head"],
    url: '//judge.localhost/v2/{competition}/serc/{serc}/mark/notes',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::notes
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:334
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/notes'
*/
notes.url = (args: { competition: string | number, serc: string | number } | [competition: string | number, serc: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            competition: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        competition: args.competition,
        serc: args.serc,
    }

    return notes.definition.url
            .replace('{competition}', parsedArgs.competition.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::notes
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:334
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/notes'
*/
notes.get = (args: { competition: string | number, serc: string | number } | [competition: string | number, serc: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: notes.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DigitalJudge\SERC\SERCJudgeController::notes
* @see app/Http/Controllers/DigitalJudge/SERC/SERCJudgeController.php:334
* @route '//judge.localhost/v2/{competition}/serc/{serc}/mark/notes'
*/
notes.head = (args: { competition: string | number, serc: string | number } | [competition: string | number, serc: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: notes.url(args, options),
    method: 'head',
})

const mark = {
    next: Object.assign(next, next),
    entity: Object.assign(entity, entity),
    store: Object.assign(store, store),
    notes: Object.assign(notes, notes),
}

export default mark