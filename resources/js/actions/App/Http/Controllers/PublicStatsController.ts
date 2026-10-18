import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PublicStatsController::clubs
* @see app/Http/Controllers/PublicStatsController.php:26
* @route '//stats.localhost'
*/
export const clubs = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: clubs.url(options),
    method: 'get',
})

clubs.definition = {
    methods: ["get","head"],
    url: '//stats.localhost',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicStatsController::clubs
* @see app/Http/Controllers/PublicStatsController.php:26
* @route '//stats.localhost'
*/
clubs.url = (options?: RouteQueryOptions) => {
    return clubs.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicStatsController::clubs
* @see app/Http/Controllers/PublicStatsController.php:26
* @route '//stats.localhost'
*/
clubs.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: clubs.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicStatsController::clubs
* @see app/Http/Controllers/PublicStatsController.php:26
* @route '//stats.localhost'
*/
clubs.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: clubs.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicStatsController::club
* @see app/Http/Controllers/PublicStatsController.php:33
* @route '//stats.localhost/clubs/{clubName}'
*/
export const club = (args: { clubName: string | number } | [clubName: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: club.url(args, options),
    method: 'get',
})

club.definition = {
    methods: ["get","head"],
    url: '//stats.localhost/clubs/{clubName}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicStatsController::club
* @see app/Http/Controllers/PublicStatsController.php:33
* @route '//stats.localhost/clubs/{clubName}'
*/
club.url = (args: { clubName: string | number } | [clubName: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { clubName: args }
    }

    if (Array.isArray(args)) {
        args = {
            clubName: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        clubName: args.clubName,
    }

    return club.definition.url
            .replace('{clubName}', parsedArgs.clubName.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicStatsController::club
* @see app/Http/Controllers/PublicStatsController.php:33
* @route '//stats.localhost/clubs/{clubName}'
*/
club.get = (args: { clubName: string | number } | [clubName: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: club.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicStatsController::club
* @see app/Http/Controllers/PublicStatsController.php:33
* @route '//stats.localhost/clubs/{clubName}'
*/
club.head = (args: { clubName: string | number } | [clubName: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: club.url(args, options),
    method: 'head',
})

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

/**
* @see \App\Http\Controllers\PublicStatsController::compare
* @see app/Http/Controllers/PublicStatsController.php:66
* @route '//stats.localhost/compare/{team1}/{team2}'
*/
export const compare = (args: { team1: string | number, team2: string | number } | [team1: string | number, team2: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compare.url(args, options),
    method: 'get',
})

compare.definition = {
    methods: ["get","head"],
    url: '//stats.localhost/compare/{team1}/{team2}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicStatsController::compare
* @see app/Http/Controllers/PublicStatsController.php:66
* @route '//stats.localhost/compare/{team1}/{team2}'
*/
compare.url = (args: { team1: string | number, team2: string | number } | [team1: string | number, team2: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            team1: args[0],
            team2: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        team1: args.team1,
        team2: args.team2,
    }

    return compare.definition.url
            .replace('{team1}', parsedArgs.team1.toString())
            .replace('{team2}', parsedArgs.team2.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicStatsController::compare
* @see app/Http/Controllers/PublicStatsController.php:66
* @route '//stats.localhost/compare/{team1}/{team2}'
*/
compare.get = (args: { team1: string | number, team2: string | number } | [team1: string | number, team2: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compare.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\PublicStatsController::compare
* @see app/Http/Controllers/PublicStatsController.php:66
* @route '//stats.localhost/compare/{team1}/{team2}'
*/
compare.head = (args: { team1: string | number, team2: string | number } | [team1: string | number, team2: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: compare.url(args, options),
    method: 'head',
})

const PublicStatsController = { clubs, club, team, compare }

export default PublicStatsController