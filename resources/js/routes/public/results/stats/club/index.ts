import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\PublicStatsController::team
* @see app/Http/Controllers/PublicStatsController.php:49
* @route '//stats.localhost/clubs/{clubName}/{teamName}'
*/
export const team = (args: { clubName: string | number, teamName: string | number } | [clubName: string | number, teamName: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: team.url(args, options),
    method: 'get',
})

team.definition = {
    methods: ["get","head"],
    url: '//stats.localhost/clubs/{clubName}/{teamName}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicStatsController::team
* @see app/Http/Controllers/PublicStatsController.php:49
* @route '//stats.localhost/clubs/{clubName}/{teamName}'
*/
team.url = (args: { clubName: string | number, teamName: string | number } | [clubName: string | number, teamName: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            clubName: args[0],
            teamName: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        clubName: args.clubName,
        teamName: args.teamName,
    }

    return team.definition.url
            .replace('{clubName}', parsedArgs.clubName.toString())
            .replace('{teamName}', parsedArgs.teamName.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicStatsController::team
* @see app/Http/Controllers/PublicStatsController.php:49
* @route '//stats.localhost/clubs/{clubName}/{teamName}'
*/
team.get = (args: { clubName: string | number, teamName: string | number } | [clubName: string | number, teamName: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: team.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicStatsController::team
* @see app/Http/Controllers/PublicStatsController.php:49
* @route '//stats.localhost/clubs/{clubName}/{teamName}'
*/
team.head = (args: { clubName: string | number, teamName: string | number } | [clubName: string | number, teamName: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: team.url(args, options),
    method: 'head',
})

const club = {
    team: Object.assign(team, team),
}

export default club