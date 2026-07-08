import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\EmailVerificationPromptController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationPromptController.php:16
* @route '/verify-email'
*/
export const __invoke = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: __invoke.url(options),
    method: 'get',
})

__invoke.definition = {
    methods: ["get","head"],
    url: '/verify-email',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\EmailVerificationPromptController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationPromptController.php:16
* @route '/verify-email'
*/
__invoke.url = (options?: RouteQueryOptions) => {
    return __invoke.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\EmailVerificationPromptController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationPromptController.php:16
* @route '/verify-email'
*/
__invoke.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: __invoke.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\EmailVerificationPromptController::__invoke
* @see app/Http/Controllers/Auth/EmailVerificationPromptController.php:16
* @route '/verify-email'
*/
__invoke.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: __invoke.url(options),
    method: 'head',
})

const EmailVerificationPromptController = { __invoke }

export default EmailVerificationPromptController