import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PublicResultsController::entityNotes
* @see app/Http/Controllers/PublicResultsController.php:228
* @route '//results.localhost/{comp}/serc/{event}/notes/{entity_id}'
*/
export const entityNotes = (args: { comp: number | { id: number }, event: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, event: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: entityNotes.url(args, options),
    method: 'get',
})

entityNotes.definition = {
    methods: ["get","head"],
    url: '//results.localhost/{comp}/serc/{event}/notes/{entity_id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicResultsController::entityNotes
* @see app/Http/Controllers/PublicResultsController.php:228
* @route '//results.localhost/{comp}/serc/{event}/notes/{entity_id}'
*/
entityNotes.url = (args: { comp: number | { id: number }, event: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, event: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            comp: args[0],
            event: args[1],
            entity_id: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        comp: typeof args.comp === 'object'
        ? args.comp.id
        : args.comp,
        event: typeof args.event === 'object'
        ? args.event.id
        : args.event,
        entity_id: args.entity_id,
    }

    return entityNotes.definition.url
            .replace('{comp}', parsedArgs.comp.toString())
            .replace('{event}', parsedArgs.event.toString())
            .replace('{entity_id}', parsedArgs.entity_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicResultsController::entityNotes
* @see app/Http/Controllers/PublicResultsController.php:228
* @route '//results.localhost/{comp}/serc/{event}/notes/{entity_id}'
*/
entityNotes.get = (args: { comp: number | { id: number }, event: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, event: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: entityNotes.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicResultsController::entityNotes
* @see app/Http/Controllers/PublicResultsController.php:228
* @route '//results.localhost/{comp}/serc/{event}/notes/{entity_id}'
*/
entityNotes.head = (args: { comp: number | { id: number }, event: number | { id: number }, entity_id: string | number } | [comp: number | { id: number }, event: number | { id: number }, entity_id: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: entityNotes.url(args, options),
    method: 'head',
})

const serc = {
    entityNotes: Object.assign(entityNotes, entityNotes),
}

export default serc