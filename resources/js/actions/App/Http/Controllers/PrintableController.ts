import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PrintableController::index
* @see app/Http/Controllers/PrintableController.php:13
* @route '/comps/{comp}/printables'
*/
export const index = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/printables',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PrintableController::index
* @see app/Http/Controllers/PrintableController.php:13
* @route '/comps/{comp}/printables'
*/
index.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PrintableController::index
* @see app/Http/Controllers/PrintableController.php:13
* @route '/comps/{comp}/printables'
*/
index.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PrintableController::index
* @see app/Http/Controllers/PrintableController.php:13
* @route '/comps/{comp}/printables'
*/
index.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

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
* @see \App\Http\Controllers\PrintableController::printSMS
* @see app/Http/Controllers/PrintableController.php:25
* @route '/comps/{comp}/printables/serc-marking-pack'
*/
export const printSMS = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printSMS.url(args, options),
    method: 'get',
})

printSMS.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/printables/serc-marking-pack',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PrintableController::printSMS
* @see app/Http/Controllers/PrintableController.php:25
* @route '/comps/{comp}/printables/serc-marking-pack'
*/
printSMS.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return printSMS.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PrintableController::printSMS
* @see app/Http/Controllers/PrintableController.php:25
* @route '/comps/{comp}/printables/serc-marking-pack'
*/
printSMS.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printSMS.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PrintableController::printSMS
* @see app/Http/Controllers/PrintableController.php:25
* @route '/comps/{comp}/printables/serc-marking-pack'
*/
printSMS.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: printSMS.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PrintableController::printCTP
* @see app/Http/Controllers/PrintableController.php:18
* @route '/comps/{comp}/printables/chief-timekeeper-pack'
*/
export const printCTP = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printCTP.url(args, options),
    method: 'get',
})

printCTP.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/printables/chief-timekeeper-pack',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PrintableController::printCTP
* @see app/Http/Controllers/PrintableController.php:18
* @route '/comps/{comp}/printables/chief-timekeeper-pack'
*/
printCTP.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return printCTP.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PrintableController::printCTP
* @see app/Http/Controllers/PrintableController.php:18
* @route '/comps/{comp}/printables/chief-timekeeper-pack'
*/
printCTP.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printCTP.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PrintableController::printCTP
* @see app/Http/Controllers/PrintableController.php:18
* @route '/comps/{comp}/printables/chief-timekeeper-pack'
*/
printCTP.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: printCTP.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PrintableController::printMarshalling
* @see app/Http/Controllers/PrintableController.php:37
* @route '/comps/{comp}/printables/marshalling'
*/
export const printMarshalling = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printMarshalling.url(args, options),
    method: 'get',
})

printMarshalling.definition = {
    methods: ["get","head"],
    url: '/comps/{comp}/printables/marshalling',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PrintableController::printMarshalling
* @see app/Http/Controllers/PrintableController.php:37
* @route '/comps/{comp}/printables/marshalling'
*/
printMarshalling.url = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return printMarshalling.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PrintableController::printMarshalling
* @see app/Http/Controllers/PrintableController.php:37
* @route '/comps/{comp}/printables/marshalling'
*/
printMarshalling.get = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: printMarshalling.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PrintableController::printMarshalling
* @see app/Http/Controllers/PrintableController.php:37
* @route '/comps/{comp}/printables/marshalling'
*/
printMarshalling.head = (args: { comp: number | { id: number } } | [comp: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: printMarshalling.url(args, options),
    method: 'head',
})

const PrintableController = { index, sercSheets, printSMS, printCTP, printMarshalling }

export default PrintableController