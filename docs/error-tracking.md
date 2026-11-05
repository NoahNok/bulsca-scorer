# Error tracking

Production errors go to [Sentry](https://sentry.io), in one project covering Laravel and the Svelte apps (Digital
Judge and Management). Each side has its own client key, so each can have its own rate limit. With no DSN set,
nothing is sent, so local development and tests stay out of it.

## Setup

1. In Sentry, create one project, and a second client key on it (Project Settings, Client Keys).
2. Add these to `.env` on the VPS:

   ```dotenv
   SENTRY_LARAVEL_DSN=     # one client key's DSN
   VITE_SENTRY_DSN=        # the other key's DSN (built into the frontend, so public by design)

   # Readable frontend stack traces. Without a token, source maps are skipped
   SENTRY_AUTH_TOKEN=      # an org token with the project:releases and org:read scopes. Keep it secret
   SENTRY_ORG=             # the organisation's slug
   SENTRY_PROJECT=         # the project's slug

   LOG_LEVEL=warning       # keeps scoring's debug logs and routine info logs out of the log files

   # Logs in Sentry, as well as the log files
   SENTRY_ENABLE_LOGS=true
   SENTRY_LOG_LEVEL=info   # separate from LOG_LEVEL, or Sentry would miss the info logs. Unset, it follows LOG_LEVEL
   LOG_STACK=daily,sentry_logs
   ```

3. Run `./deploy.sh`. The DSNs and token are read while it caches config and builds the frontend.
4. Run `php artisan sentry:test` and check the event arrives. For the frontend, run
   `setTimeout(() => { throw new Error("Sentry test") })` in the browser console on a Digital Judge or Management page.

## Settings in Sentry

The code can't set these:

- **Rate limit:** set one on each client key (Project Settings, Client Keys), so one bad bug or noisy browser errors
  can't use the month's quota. When quota runs out, new errors are dropped until it resets.
- **Spike protection:** leave it on.
- **Alerts:** alert on new issues, so a bug is seen on its first occurrence.
- **Privacy:** in Security & Privacy, turn on "Prevent Storing of IP Addresses" and leave the default data scrubbers on.

## What's sent

Errors, and backend logs at `SENTRY_LOG_LEVEL` and above if logs are turned on. Tracing, session replay, profiling and metrics
are off, as they're what use quota per request or session. Frontend logs aren't sent.

Each error has its stack trace, the url without its query string, the release (the git commit) and these tags. To see
one side, filter on a tag only it has: `has:app` for the frontend, `has:subdomain` for the backend.

| Tag | Values | Where |
| --- | --- | --- |
| `subdomain` | `judge`, `app`, `live`, `results`, `stats`, `whatif`, `main`, `console` | Backend |
| `app` | `judge`, `manage` | Frontend |
| `page` | The Inertia page, like `Manage/Competition/ResultSheet` | Frontend |
| `competition` | The competition's ID | Both |
| `user_role` | `admin`, `user`, `guest`, and `judge` on the backend | Both |

Signed-in users are identified by ID only.

Request bodies, query strings, headers, cookies, IP addresses, SQL bindings and stack frame variables aren't sent
(`data_collection` in `config/sentry.php`, `beforeSend` in `resources/js/lib/sentry.ts`).

An exception's message is still sent, and can hold personal data: a `QueryException`'s includes the query's values.
Keep names and emails out of messages you write.

## Logs

Logs are searchable in Sentry's Logs view, and use their own quota, separate from errors. Each reported exception is
also logged, so it appears in both.

Each log gets `subdomain`, `competition` and `user_role` attributes, the same as the error tags above, and `user.id`
(`ErrorContext::applyToLog`).

A log's message and context are sent as they are, unlike an error's request data. To keep something personal in the log
file only, use `Log::channel('daily')`, as `ResultSheetsController` does.

### Writing a log

Use the `Log` facade, and log what would answer a question in production: not every step, as a log still has to be
useful thousands of times over.

| Level | For | Example |
| --- | --- | --- |
| `info` | Something changed, or a job's summary | `ResultSheetsController::publish`, `PruneRetainedData` |
| `warning` | A failure that was handled | `ResultSheetsController::show` |
| `error` | Don't write these by hand: `report($e)`, which logs it too and opens an issue with a stack trace | |

```php
Log::info('Competition results publishing changed', ['results.published' => true]);
```

- Keep the message the same each time, and put what varies in the context. Only the context can be filtered on.
- Name context keys `domain.field`, and reuse existing ones.
- Send IDs, never names or emails. Leave out exceptions' messages, which can hold personal data.
- Don't repeat what's added for you, like the competition on a request. A command has no request, so its logs need to
  pass any competition themselves.

## Not covered

Public Blade pages (the main domain, `results.`, `stats.` and `whatif.`) don't load the Svelte apps, so their browser
errors aren't tracked. Their server errors are.

## Writing code

- If you catch an exception and carry on, call `report($e)` unless the failure is expected, so it isn't hidden.
- Don't report what the user is already being shown and can fix: an Organiser's invalid formula on their own Results
  sheet (`ResultSheetsController`) is logged, not reported.
- Validation errors, 404s, failed sign-ins and permission errors aren't reported, as Laravel treats them as normal.
- Errors that repeat on every page view use up quota, so limit them in `Handler::throttle()`, as `ScoringException` is.
