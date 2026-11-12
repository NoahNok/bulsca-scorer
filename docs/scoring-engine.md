# The scoring engine: evaluation and rebuild plan

Written 2026-11-11 on `feature/scoring-engine-rebuild`, with the tests and tooling that make a
rebuild safe. The evaluation below is the engine as it was found; where a step of the rebuild has
since dealt with a point, it says so. Progress: steps 0, 1 and 2 are done (see [Steps](#steps)).

## In short

- **The engine is correct for every competition it has scored.** Run locally against a copy of the
  live database it reproduces what scoring.events shows (checked row by row for Loughborough
  2025-26's overall sheet, Nationals 2025's Master sheet and a Yorkshire Regional sheet with
  tie-breaks).
- **It is slower than it needs to be, but not slow.** A sheet takes 50–250 ms and 90–230 queries
  uncached; two thirds or more of those queries are one repeated League lookup. All of Nationals
  2025 takes about 3.3 s and several thousand queries from cold.
- **It is hard to change safely.** The logic is spread over five Eloquent models, ranking is
  written out four times, formulas read ORM objects directly, and until this branch no test
  checked a computed score.
- **Its cache can serve stale results**, because it's cleared by model events that don't cover
  every way a result can change (see [Quirks](#quirks), Q8).
- **There are now 116 tests around it**: 23 real competitions scored again and compared in full, 89
  small hand-worked cases and 4 that hold scoring an Event to a query budget. A tool compares the
  engine's output for every competition in a database before and after a change.
- The rebuild is six steps, each of which ships on its own with all of the above still passing.

## How it works today

Scoring has three levels, each built on the one below.

```
speed_results / serc_results          penalties, disqualifications, seeds
        │                                        │
        ▼                                        ▼
1. Event      raw result ──► violations ──► resolved result ──► points ──► place
              (per Entity)   auto pens/DQs,  (penalty function)  (equation)  (rank, ties,
                             grouping                                        rank equation)
        │
        ▼
2. Results sheet   each Event's results (for the sheet's League) ──► Formula ──► × Event weight
                   ──► summed per Entity ──► ranked, with tie-breaks
        │
        ▼
3. Master sheet    each Club's best place in every sheet ──► summed ──► ranked
```

| Level | Code | What it does |
| --- | --- | --- |
| Formula language | `App\Scoring\Engine\FormulaEvaluator` (until step 1 `ScoringEngine`, a second class inside `ScoringSchema.php`) | Symfony ExpressionLanguage plus `FILTER`, `MAXIMUM`, `MINIMUM`, `COUNT`, `CEIL`, `FLOOR`, `RANK`, `RANKMEDIAN`. Works out global variables, local variables and the equation for a set of results (`process`). Until step 2 it also ran auto penalties, auto DQs and the penalty function on the models (`applyViolations`). |
| Event (since step 2) | `Event::getRankedResults()` → `App\Scoring\Loading\EventLoader` → `App\Scoring\Engine\EventScorer` → `LoadedEvent::rankedResults()` | The loader reads a speed event's times or a SERC's Marks (summed by marking point weight per Entry), with penalties, DQs, seeds and each Entry's Leagues, into a plain `EventInput`: 7 to 12 queries. The scorer, with no database, filters to a League, applies auto penalties, auto DQs and the penalty function, groups, works out points and ranks. The rows are handed back as `RankedResult`s. |
| Speed event (until step 2) | `CompetitionSpeedEvent::getRawResults / getResolvedResults / getRankedResults` | Loaded times, penalties, DQs and seed times; handed them to the formula language; ranked. `getRawResults` remains, for callers that want results before scoring. |
| SERC (until step 2) | `SERC::getRawResults / getResolvedResults / getRankedResults` | Loaded Marks, summed them by marking point weight per Entity, then the same as a speed event. `getRawResults` remains. |
| Results sheet | `ResultSchema::getResults` | Scores each of its Events (for its League), applies the sheet's Formula to each Event's results, multiplies by the Event's weight, sums per Entity, ranks. Cached per sheet, forever. |
| Master sheet | `MasterSchema::getResults` | Takes each Club's first (best) row in each of its sheets and sums the places. |
| Results in transit | `app/DTO/`: `Result` → `ResolvedResult` → `RankedResult` → `ScaledResult`, `OverallResult`, `MasterResult` | A new object at each stage, built by copying the last one field by field. Since step 2 an Event is scored on one `ScoredResult` per Entry and only its finished rows become `RankedResult`s. |

A formula is stored as JSON on `scoring_schemas.schema` (Events) or `result_schemas.schema`
(sheets): `equation`, `global_variables`, `local_variables`, `penalty_func`, `force_penalty`,
`auto_penalties`, `auto_disqualifications`, `rank_higher`, `rank_equation`,
`allow_disqualified_to_rank`, `group_by`; sheets add `league`, `ignore_dq`, `group_on`,
`break_ties`. The live database holds 283 Event formulas, about 30 distinct.

Who asks for results: the public Results pages and their JSON (`Landing\ResultsController`), the
SERC breakdown, Management's sheet and Master sheet pages, results PDFs (`ResultsPdfCreator`),
and the Live pages. Event results are worked out afresh on every request; only sheets are cached.

## Evaluation

### Correctness

The numbers are right, and the tests on this branch now prove that for 23 real competitions. What
is wrong sits at the edges; these are listed under [Quirks](#quirks) with what each one affects.

Two things worth knowing that aren't quirks of the maths:

- **The order of exact ties isn't defined.** Entries with the same place and points come out in
  whatever order the database returned them, and the League-filtered query has no `ORDER BY`. The
  same competition listed tied teams in a different order in two copies of the database. Places
  and points were identical. (With tie-breaks on a sheet this does decide a place: see Q7.)
- **An Event with no formula has no results**, silently. Fifteen competitions in the live database
  have at least one such Event (see [Found along the way](#found-along-the-way)).

### Efficiency

Measured on this machine against a copy of the live database, with nothing cached:

| What | Time | Queries |
| --- | --- | --- |
| One league speed event (28 teams) | 15–30 ms | 15 |
| One league SERC (28 teams, ~100 marking points) | 50–55 ms | 14 |
| Loughborough 2025-26 overall sheet (5 Events) | 245 ms | 231 |
| One Nationals 2025 speed event (133 competitors) | 150–160 ms | 207 |
| One Nationals 2025 sheet (4 Events, one League) | 60–135 ms | 90–185 |
| Everything for Nationals 2025 (4 Events × 19 views, 18 sheets, 2 Master sheets) | 3.3 s | several thousand |
| Everything for a BULSCA league competition | 0.7–1.2 s | ~1,000 |

Where it goes:

1. **One League query per Entity, per Event, per sheet.** `Entity::getGrouping()` runs
   `leagues()->orderBy('id')->first()` for every result. It is 150 of the 231 queries in the
   Loughborough sheet and 195 of the 206 in a Nationals speed event. *Removed in step 2: an Event
   reads its Entries' Leagues in one query.*
2. **Every sheet scores its Events again.** Nationals has 18 sheets over the same 4 Events, so each
   Event is loaded and scored 18 times (plus once per League on the Events tab). Nothing is shared
   between sheets, or between a sheet and the Master sheet's other sheets.
3. **A League view re-queries rather than filters.** The League's rows are a subset of rows already
   loaded for the whole Event. *Fixed in step 2: `EventScorer` filters the Event's rows.*
4. **About 60% of the time is PHP, not the database**: Eloquent hydrating a model per Mark (a league
   SERC is ~2,900 rows), a DTO copied four times per result, and expressions parsed again on each
   use (`evaluateInstance` parses the rank equation once per result; `FILTER` evaluates its
   condition from the string for every item).
5. **`RANK` scans the sorted list for every result** (quadratic), and both rank functions rebuild
   a cache key by sorting and hashing every id on every call.
6. **`Log::debug` runs for every Event and every global variable** on every scoring. *Removed in
   step 1.*

None of this is slow enough to hurt a results page today, because sheets are cached. It matters on
the day, when every Mark clears the cache and the Live and Results pages are being refreshed, and
it is the reason the cache exists at all, with the staleness that brings.

### Maintainability

- **The engine is inside the models.** Loading, scoring, ranking, grouping and caching are methods
  on `CompetitionSpeedEvent`, `SERC`, `ResultSchema` and `MasterSchema`, next to heats, draws,
  judging and stats. `SERC.php` is 570 lines; `ScoringSchema.php` held a model and the 480-line
  `ScoringEngine` in one file (*separated in step 1*).
- **Ranking is written four times**: in `CompetitionSpeedEvent` and `SERC` (30 identical lines but
  for one condition: a SERC always ties its DQs, a speed event only when DQs can't rank), and again
  in `ResultSchema` and `MasterSchema`. *Step 2 replaced the first two with `EventScorer::rank()`.*
- **Formulas reach into the ORM.** `item` is a DTO holding Eloquent models, so a formula can read
  `item.entity.st_time` (two old formulas do) and `item.penalties` is a collection of `Penalty`
  models; auto penalties are made by constructing unsaved `Penalty` models. What a formula may
  rely on is whatever happens to be public. *Since step 2 an Event's formula reads a `ScoredResult`
  and a plain `Entry`; a sheet's Formula still reads the DTOs.*
- **Fields get lost between stages.** Each DTO is built by copying the last positionally. `seed`
  isn't copied past the violations stage, so the equation sees 0 (Q4); `combined` and
  `total_marking_points` don't reach a sheet.
- **Settings are untyped arrays** read with `?? default` wherever they're used, so the defaults
  (`rank_higher` true, `group_on` team, `ignore_dq` false…) are repeated and can drift.
  `applyToResults` sorted the variables by `order` and then passed the unsorted ones on, so
  variables have always run in the order they are stored and `order` has never been read. Step 1
  removed the dead sort and pinned that in a test; no formula in the fixtures stores its variables
  out of order.
- **`ScoringSchema::__construct()` takes no arguments**, so `new ScoringSchema([...])` and
  `ScoringSchema::create([...])` silently ignore their attributes. *Fixed in step 1.*
- **A `ScoringException` formats every result in its context when it's constructed**, looking up
  each Entity's name (cache, then database). An error in a formula is the worst moment for that.
  *Fixed in step 1: the context is formatted when it's asked for.*
- **Several callers are already broken.** `StatsManager`, `ResultsToJSONController`, the What-If
  controller and four old Blade templates call `$event->getResults()`, which no Event has had for
  some time.
- Morph types are stored two ways (`App\Models\SERC` and `\App\Models\SERC`), and tie-breaks two
  ways (bare ids and `{type, id}`).

### Testability

Before this branch, `ResultsPagesTest` was the only test that looked at a computed place, and
nothing checked points. The formula language can run without a database, but everything above it
can't, and even its error path needed one (see the `ScoringException` point above). There was no
way to tell whether a change to the engine altered a past competition short of looking at it.

## The safety net (on this branch)

### Golden competitions: `tests/Golden`

Each folder in `tests/Golden/fixtures` is one real competition:

- `input.json`: every row the engine reads to score it (entries, Leagues, times, Marks, penalties,
  DQs, seeds, formulas, sheets), with the ids it had live. Competitors are renamed "Competitor 1…";
  clubs and teams keep their names, except a name that contains a competitor's full name. Pins,
  accounts and timestamps aren't exported.
- `expected.json`: everything the engine works out for it: each Event's ranked results for
  everyone and for each League, every Results sheet with its per-Event breakdown, every Master
  sheet.

`GoldenCompetitionsTest` loads the input into the test database, scores it and compares. Places,
codes, names, row order and every number must be identical, to the last digit. Entries that
tie exactly are compared in Entity order, because the engine doesn't define their order.

`EventQueryBudgetTest` loads two of them and counts the queries scoring each Event takes: at most
12 for Nationals 2025 (133 competitors a speed event, 19 Leagues) and 10 for Loughborough 2025-26
(28 teams, with seed times), and no more for a League view than for the whole Event.

The 23 competitions (3.6 MB, about 35 s to run):

| | |
| --- | --- |
| BULSCA league | Nottingham, Warwick, Birmingham, Bristol, Southampton, Loughborough and the Championships 2025-26; Nottingham 2026-27; Bristol 2022-23 (the oldest, for old-style data) |
| RLSS Nationals | Nationals 2025 (18 sheets, 2 Master sheets), National Club SERC 2025 |
| 2026 regionals | East, South East, Yorkshire, East Midlands, Scotland, South, West Midlands, Wales, North West, West & South West, and the May West Midlands regional |
| Other | MegaVarsity (5 speed events, teams without clubs) |

The fixtures were checked two ways: scoring a fixture gives exactly what scoring the same
competition in the full database copy gives (0 differences), and three of them were compared with
the live site.

**Adding a competition.** Load a copy of the live database somewhere that isn't your dev database,
point `DB_*` at it, then:

```
php artisan scoring:golden-export 96 97          # writes tests/Golden/fixtures/<name>-<id>/input.json
UPDATE_GOLDEN=1 php artisan test --testsuite=Golden   # records expected.json with the current engine
```

Read the new fixture's team names before committing it: a team named after someone who has never
been entered as a competitor can't be detected.

**When a golden test fails**, the engine now scores a past competition differently. If that's the
point of the change, re-record with `UPDATE_GOLDEN=1`, and review the diff of `expected.json` (one
result per line) as the list of results that moved.

### Hand-worked cases: `tests/Feature/Scoring`, `tests/Unit/Scoring`

Small made-up competitions, one rule per test, with the answer worked out by hand in a comment.
Those in `tests/Feature/Scoring` go through the same entry points as the pages do, so they hold
while the engine behind them changes. Those in `tests/Unit/Scoring` take one class on its own, with
no database or framework.

| File | Covers |
| --- | --- |
| `SpeedEventScoringTest` (16) | Time to points, ranking either way, shared places, DQs last, DQs allowed to rank, DNF/DNS/OOT, penalty function, forced penalty function, auto penalties from seed times, each entry's own seed time, rank equation, `RANKMEDIAN`, pairs combined by `group_by`, League views |
| `SercScoringTest` (5) | Weighted Marks, scoring against the best, DQs, ranking Marks into places, League views |
| `ResultSheetScoringTest` (12) | Event weights, per-Event breakdown, summing places, the BULSCA spread Formula, DQs and `ignore_dq`, tie-breaks, missing Events, League sheets, grouping on Club, cache refresh |
| `MasterSheetScoringTest` (3) | Summed places, best entry per sheet, default for a missing Club, excluded Clubs |
| `EngineQuirksTest` (8) | Each quirk below but Q2 and Q3, pinned as it behaves today |
| `EventScorerTest` (20, no database or framework) | `EventScorer` on inputs built by hand: ranking either way, shared places, a time stored as a string, DQs last, DQs allowed to rank, a SERC tying its DQs regardless, League filtering, penalty function, forced penalty function, auto penalties from seeds (and none: Q6), an auto DQ's code (Q1), the seed in the equation (Q4), pairs combined by `group_by` and what keeps results apart, rank equation, an empty League (Q9), `item.entity.<column>`, a missing equation |
| `FormulaEvaluatorTest` (18, no database or framework) | Each built-in function, global and local variables and the order they run in, target field, a rank equation, what a broken formula reports, and quirks Q2 and Q3 |
| `ScoringExceptionTest` (5, no database or framework) | A failed formula's context: described when asked for, once, without touching the results |
| `ScoringSchemaTest` (2, no database or framework) | A formula made with its name and settings, and its settings read with their defaults |

### Whole-database comparison: `scoring:snapshot`

The fixtures are 23 competitions; the live database has 66. To check all of them:

```
php artisan scoring:snapshot --out=storage/app/scoring-before       # on the old code
php artisan scoring:snapshot --compare=storage/app/scoring-before   # on the new code
```

The second prints each competition as `same` or lists what differs, and exits non-zero if anything
changed. Run it against a copy of the live database before deploying any step below. The recording
holds competitors' real names, so keep it out of git (`storage/app` is ignored).

`App\Scoring\Golden\CompetitionSnapshot` is the only code in the tooling that calls the engine.
When the entry points change, re-point its three `*Rows()` methods.

## Quirks

Behaviour that looks unintended. Each is pinned in `EngineQuirksTest` (Q2 and Q3 in
`FormulaEvaluatorTest`) so a rebuild can't change it by accident. "Affects" is from applying the fix to the current engine and comparing all 66
competitions in the live database.

| | Quirk | Affects | Suggested |
| --- | --- | --- | --- |
| Q1 | An auto DQ is given the code of the **last auto penalty**, not its own (`$autoPen['code']` in the auto-DQ loop). With no auto penalties at all, scoring the Event throws. | Nothing: the only formulas with an auto DQ use 900 for both. | Fix |
| Q2 | `RANK` and `RANKMEDIAN` cache by Entity ids and attribute but **not direction**, so asking for `'ASC'` and `'DESC'` on the same results returns the first answer twice. The cache also outlives a change to the results within one request. | Nothing | Fix |
| Q3 | `RANKMEDIAN` groups values by array key, which truncates floats: **22.5 and 22.0 marks are treated as tied**. Times are whole milliseconds so speed events are safe; a SERC ranked with `RANKMEDIAN` and fractional marking point weights is not. PHP 8 reports the truncation as a deprecation; PHP 9 is due to make it an error. | Nothing yet | Fix (before it does) |
| Q4 | `item.seed` is readable in auto penalties and the penalty function but is **0 in the equation**. | Nothing uses it there | Fix |
| Q5 | Seed times are always read from the competition's **first speed event**, even with seeds per event turned on. | Nothing: no competition has used seeds per event | Fix |
| Q6 | An Entity with **no seed time is scored as if its seed were 0**, so the BULSCA Swim & Tow auto penalty gives it `1 + FLOOR(time / 15000)` P900s. | Whatever was scored that way stays that way | **Keep** (decided 2026-11-11) |
| Q7 | A sheet with tie-breaks gives **entries that tie on everything different places**, in database order. Which one is placed higher is not defined. | 133 sheets use tie-breaks; none in the fixtures currently tie all the way | **Keep** (decided 2026-11-11). The rebuild's loader must read rows in id order, which is the order the engine sees today, so these places don't move. |
| Q8 | A sheet's cached results are cleared when a result or Mark is **edited**, and on any penalty or DQ, but **not** when a Mark or time is first entered, nor when a marking point's weight, a formula, a League's members or an entry changes. | Shows stale standings until something else clears the cache | Fixed by step 4 |
| Q9 | Scoring an Event for a League with no entries evaluates the formula anyway, which can divide by zero (Champs 2023-24's Rope Throw for its empty "Squad" League). | That one view errors | Fix: no entries, no results |
| Q10 | The Master sheet ignores each sheet's `weight`, and `sum_over` anything but `position` reads a property that doesn't exist. | Nothing: every weight is 1 and every Master sheet sums places | Fix or remove the settings |

## The rebuild

### What it should look like

Three parts with one direction of dependency, all under `app/Scoring/`:

1. **Inputs** (`App\Scoring\Input`). Plain, read-only data: an `Entry` (id, type, name, club, team,
   Leagues), an Event's rows (entry, raw result or summed Marks, penalties and DQs as codes, seed),
   a `Formula` and a `SheetDefinition` as typed objects with their defaults in one place. One
   loader builds all of a competition's inputs in a fixed number of queries (about 12, however many
   entries, Events or sheets there are).
2. **The engine** (`App\Scoring\Engine`). Pure functions from inputs to results, with no Eloquent,
   cache, logging or facades:
   - `FormulaEvaluator`: ExpressionLanguage and the built-in functions, each expression compiled
     once per formula.
   - `EventScorer::score(EventInput, ?League): EventResults`: violations, penalty function,
     grouping, equation, rank, rank equation. One implementation for speed events and SERCs.
   - `SheetScorer::score(SheetDefinition, EventResults[]): Standings` and `MasterScorer`.
   - One `Ranking` helper for "sort, share places on ties", used by all three.
3. **The read model** (`App\Scoring\CompetitionResults`). What pages call. Scores each (Event,
   League) once and shares it between every sheet, Master sheet and page that needs it; caches by a
   **results version** on the competition, bumped by every write that can change a result. A cached
   result is then right or absent, never stale.

Stored formulas keep working unchanged: same language, same functions, and `item` exposes the same
names (`result`, `resolvedResult`, `points`, `position`, `seed`, `penalties`, `disqualifications`,
`isDisqualified()`, `isDNF()`, `isCombined()`, `hasPenalties()`, `entity`). That list becomes the
documented contract rather than "whatever is public". No stored formula is migrated.

Arithmetic stays in the same order (an Entity's Events summed in sheet order, weight applied after
the Formula), so results match to the last digit, which is what the golden tests demand.

Expected gain: a competition's inputs load once, so Nationals goes from several thousand queries
to about a dozen, and from 3.3 s to a few hundred ms cold; a page that needs one sheet no longer
pays for hydrating thousands of Mark models.

### Steps

Each step is one PR. Each must pass the golden suite, the hand-worked cases and a
`scoring:snapshot --compare` against a copy of the live database before it merges.

**0. Safety net.** Done on this branch.

**1. Pull the formula language out.** Done 2026-11-12, as written below. Checked with the golden
suite and the hand-worked cases; **the whole-database comparison is still to run** (it needs the
live copy loaded into a database of its own). Move `ScoringEngine` out of `ScoringSchema.php` into
`App\Scoring\Engine\FormulaEvaluator` with its functions, as it is, quirks included. Give
`ScoringSchema` a normal constructor. Make `ScoringException` hold its context and format it only
when reported. Drop the `Log::debug` calls. `ScoringEngineTest` and Q2/Q3 in `EngineQuirksTest`
move to pure unit tests.

**2. One Event scorer on plain inputs.** Done 2026-11-12. Checked with the golden suite and the
hand-worked cases; **the whole-database comparison is still to run**, for this step and step 1
together. What was built: `App\Scoring\Input` (`Entry`, `EventRow`, `EventInput`, `Formula`),
`App\Scoring\Engine\EventScorer` and `ScoredResult`, `App\Scoring\Loading\EventLoader` and
`LoadedEvent`. `Event::getRankedResults()` is the one thin call, mapping the scorer's rows back to
today's `RankedResult`, so no caller changed. `getResolvedResults()`, `applyGrouping()`, the two
ranking loops and the evaluator's model-bound `applyViolations()` are deleted (the first two earlier
than step 5 planned, as nothing else reached them). Leagues are filtered in memory. Seeds are
matched on `entity_id` without reading their Entries again (an Entry's seeds are deleted with it).
`EventScorerTest` covers the scorer on hand-built inputs and `EventQueryBudgetTest` holds the
query counts.

Measured on fixtures: a speed event entered by teams takes 10 queries and one entered by
competitors 12 (was 15–207), a SERC 7 (was 14); a 133-entry Nationals speed event 22–36 ms (was
150–160).

Known difference to confirm with the whole-database comparison: a result whose Entry no longer
exists now fails a League view too, not only the whole Event.

**3. Sheets and Master sheets on the scorer.** In progress 2026-11-12, uncommitted (as are steps
0–2). Done: `App\Scoring\Input\SheetDefinition` and `SheetEvent`; `App\Scoring\Engine\Ranking`
(also used by `EventScorer`), `SheetScorer`, `SheetEventResult`, `SheetStanding`, `MasterScorer`,
`MasterStanding`; `App\Scoring\ScoringRun` (one per request, bound `scoped` in
`AppServiceProvider`, emptied on any Eloquent save or delete, but not by a pivot `attach()` or a
query-builder write); `ResultSchema::getResults()` and `MasterSchema::getResults()` delegate and
return the same DTOs. `RankingTest` (5), `SheetScorerTest` (18) and `MasterScorerTest` (7) are
pure unit tests. Checked: Unit/Feature and Golden pass with fixtures untouched, and the
whole-database comparison of HEAD's engine against steps 1–3 gave all 66 competitions the same
(39 s old, 15 s new).

**Continue from here:**
1. Add `tests/Feature/Scoring/ScoringRunTest`: sheets sharing an Event read `speed_results` once;
   one sheet's Formula doesn't change what another is given; a model saved in between is seen by
   the next sheet scored; a query-builder write is not until `ScoringRun::forget()`;
   `Event::getRankedResults()` always scores afresh.
2. Add a sheet budget to `tests/Golden/EventQueryBudgetTest`: every sheet and Master sheet of a
   fixture scored with `getResults(true)` in one request, asserting a measured query count.
3. Make `CompetitionSnapshot::of()` call `app(ScoringRun::class)->forget()` first.
4. Update this document for step 3: "In short" test count, the "How it works today" rows for
   Results sheet and Master sheet, the Efficiency and Maintainability notes (sheets no longer
   re-score Events; ranking is written once), the test tables, and mark this step done.
5. Run all three suites, then repeat the whole-database comparison (load `scoring.sql` into a
   database of its own, record on HEAD plus the step 0 tooling, compare on the branch, both with
   `CACHE_DRIVER=array` so dev's Redis cache isn't written).
6. Commit as three: step 0 (tooling, fixtures, `tests/Feature/Scoring`), steps 1 and 2 together
   (they share files and can't be separated without rewriting an intermediate state), step 3.

**Clean up first (left over from 2026-11-12):** the `scoring_test` database holds the live dump
(real names) rather than the test schema; the next Feature or Golden run wipes and rebuilds it, so
run one. The session scratchpad
`/tmp/claude-1000/-home-noah-projects-bulsca-scorer/51bb3931-7e8a-438d-a27f-5bcc415199bc/scratchpad`
holds `before/` (the old engine's recording of all 66 competitions, with real names) and `old/`
(a 200 MB copy of HEAD's code): delete both by hand. Remove this paragraph once done.

The plan as written: Add `SheetDefinition`, `SheetScorer` and
`MasterScorer` with the shared `Ranking`. Within a request, score each (Event, League) once and
reuse it across sheets. `ResultSchema::getResults()` and `MasterSchema::getResults()` delegate,
returning today's `OverallResult` / `MasterResult`.

**4. Results version and cache.** Add `results_version` to competitions and bump it from every
write path that affects scoring: speed results, Marks, penalties and DQs, seeds, entries and their
Leagues, marking points and weights, Event formulas, sheets and Master sheets. Cache
`CompetitionResults` by competition and version; remove the tag flushes in `Resultable`,
`Violation` and `ResultSheetsController`. This is the one step that changes behaviour on purpose
(Q8): replace that quirk test with ones that each kind of write refreshes the standings.

**5. Move callers to the read model and delete the old path.** Point the public Results pages,
SERC breakdown, Management pages, PDFs and Live pages at `CompetitionResults`. Then delete
`getRawResults / getResolvedResults / getRankedResults`, the DTO chain in `app/DTO`, `applyGrouping`
on `Event`, and the dead callers of `$event->getResults()` (`StatsManager`,
`ResultsToJSONController` and its route, the old Blade templates). Leave What-If alone: it is being
rewritten and should be written against `CompetitionResults`. Re-point `CompetitionSnapshot`.

**6. Fix the quirks, one PR each.** With the engine in one place each fix is a few lines. For each:
change the pinned test to the intended behaviour, run the golden suite and the whole-database
comparison, and put the list of results that moved (expected: none, for Q1–Q5, Q9, Q10) in the PR.
Q6 and Q7 stay as they are: their tests in `EngineQuirksTest` are the specification, not a to-do.

Later, if wanted: validate a formula when it's saved by running it against sample rows rather than
only checking its syntax; and store the two morph-type spellings and tie-break formats one way.

### Decisions for you

1. **Q6** (no seed time scored as a seed of 0): decided 2026-11-11, leave as is.
2. **Q7** (entries a sheet's tie-breaks don't separate get different places): decided 2026-11-11,
   leave as is.
3. **Golden numbers**: decided 2026-11-11, they must match exactly. The tests compare to the last
   digit, with no tolerance.
4. **`ResultsToJSONController`** (`/api/results/{comp}/serc/{event}`): decided 2026-11-11, nothing
   is known to use it, so step 5 deletes it and its route.

## Found along the way

Not engine faults, but worth a look:

- **Nationals 2026 (14 November): both SERCs have no formula**, and the competition has no Results
  sheets yet. A SERC without a formula produces no results at all. Its two speed events do have
  formulas.
- **Older competitions with public results that now show none**, because their Events have no
  formula: Brum Internal May23, Sheff vs Notts Varsity 2023 and 2024, Brumwick Varsity 2024,
  Brumwick the Second 2024 (all Events); National Club SERC 2024's speed event; one of Brum vs
  Lough Varsity 2025's four speed events.
- **Two League views that error** on the public Events tab: Champs 2023-24's Rope Throw for its
  empty "Squad" League (Q9), and Brum 2024-25's SERCs for "Non-counting", whose only team was
  disqualified (the formula divides by the best non-disqualified score, and there isn't one).
- `php artisan stats:generate` can't work: `StatsManager` calls `$event->getResults()`.
