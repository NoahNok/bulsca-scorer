import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\PrintableController::sercSheets
* @see app/Http/Controllers/PrintableController.php:32
* @route '/comps/{comp}/printables/serc-sheets/{serc}'
*/
export const sercSheets = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sercSheets.url(args, options),
    method: 'get',
})

sercSheets.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/printables/serc-sheets/{serc}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PrintableController::sercSheets
* @see app/Http/Controllers/PrintableController.php:32
* @route '/comps/{comp}/printables/serc-sheets/{serc}'
*/
sercSheets.url = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            serc: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        serc: typeof args.serc === 'object'
        ? args.serc.id
        : args.serc,
    }

    return sercSheets.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{serc}', parsedArgs.serc.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PrintableController::sercSheets
* @see app/Http/Controllers/PrintableController.php:32
* @route '/comps/{comp}/printables/serc-sheets/{serc}'
*/
sercSheets.get = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sercSheets.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PrintableController::sercSheets
* @see app/Http/Controllers/PrintableController.php:32
* @route '/comps/{comp}/printables/serc-sheets/{serc}'
*/
sercSheets.head = (args: { comp: number | { id: number }, serc: number | { id: number } } | [comp: number | { id: number }, serc: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sercSheets.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PrintableController::sercMarkingPack
* @see app/Http/Controllers/PrintableController.php:25
* @route '/comps/{comp}/printables/serc-marking-pack'
*/
export const sercMarkingPack = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sercMarkingPack.url(args, options),
    method: 'get',
})

sercMarkingPack.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/printables/serc-marking-pack',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PrintableController::sercMarkingPack
* @see app/Http/Controllers/PrintableController.php:25
* @route '/comps/{comp}/printables/serc-marking-pack'
*/
sercMarkingPack.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return sercMarkingPack.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PrintableController::sercMarkingPack
* @see app/Http/Controllers/PrintableController.php:25
* @route '/comps/{comp}/printables/serc-marking-pack'
*/
sercMarkingPack.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sercMarkingPack.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PrintableController::sercMarkingPack
* @see app/Http/Controllers/PrintableController.php:25
* @route '/comps/{comp}/printables/serc-marking-pack'
*/
sercMarkingPack.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sercMarkingPack.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PrintableController::chiefTimekeeperPack
* @see app/Http/Controllers/PrintableController.php:18
* @route '/comps/{comp}/printables/chief-timekeeper-pack'
*/
export const chiefTimekeeperPack = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: chiefTimekeeperPack.url(args, options),
    method: 'get',
})

chiefTimekeeperPack.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/printables/chief-timekeeper-pack',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PrintableController::chiefTimekeeperPack
* @see app/Http/Controllers/PrintableController.php:18
* @route '/comps/{comp}/printables/chief-timekeeper-pack'
*/
chiefTimekeeperPack.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return chiefTimekeeperPack.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PrintableController::chiefTimekeeperPack
* @see app/Http/Controllers/PrintableController.php:18
* @route '/comps/{comp}/printables/chief-timekeeper-pack'
*/
chiefTimekeeperPack.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: chiefTimekeeperPack.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PrintableController::chiefTimekeeperPack
* @see app/Http/Controllers/PrintableController.php:18
* @route '/comps/{comp}/printables/chief-timekeeper-pack'
*/
chiefTimekeeperPack.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: chiefTimekeeperPack.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PrintableController::marshalling
* @see app/Http/Controllers/PrintableController.php:37
* @route '/comps/{comp}/printables/marshalling'
*/
export const marshalling = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: marshalling.url(args, options),
    method: 'get',
})

marshalling.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/printables/marshalling',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PrintableController::marshalling
* @see app/Http/Controllers/PrintableController.php:37
* @route '/comps/{comp}/printables/marshalling'
*/
marshalling.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return marshalling.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PrintableController::marshalling
* @see app/Http/Controllers/PrintableController.php:37
* @route '/comps/{comp}/printables/marshalling'
*/
marshalling.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: marshalling.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PrintableController::marshalling
* @see app/Http/Controllers/PrintableController.php:37
* @route '/comps/{comp}/printables/marshalling'
*/
marshalling.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: marshalling.url(args, options),
    method: 'head',
})

const printables = {
    sercSheets: Object.assign(sercSheets, sercSheets),
    sercMarkingPack: Object.assign(sercMarkingPack, sercMarkingPack),
    chiefTimekeeperPack: Object.assign(chiefTimekeeperPack, chiefTimekeeperPack),
    marshalling: Object.assign(marshalling, marshalling),
}

export default printables