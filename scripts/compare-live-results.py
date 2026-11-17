#!/usr/bin/env python3
"""Compare the golden fixtures' expected.json with the results a site serves.

For every fixture in tests/Golden/fixtures, fetches each results view the public Results page reads
(each Event overall and per League, each Results sheet, each Master sheet) as JSON from
<base>/competition/<id>/results/... and compares places and points with expected.json.

The public pages round points to 1 decimal place and show "DQ" instead of a disqualified entry's
points, so points are compared at 1 decimal place and, for a "DQ", only that the fixture has the
entry disqualified. Rows that tie are compared as a set, since their order isn't defined (Q7).

Usage:
    python3 scripts/compare-live-results.py                      # against https://scoring.events
    python3 scripts/compare-live-results.py --base http://localhost
    python3 scripts/compare-live-results.py --only nationals-2026-92 --refresh

Downloads are cached in storage/app/live-results/<host> (they hold real names, keep them out of
git; storage/app is ignored). Exits 1 if anything differs or can't be fetched.
"""

import argparse
import concurrent.futures
import json
import os
import sys
import urllib.parse
import urllib.request
from decimal import ROUND_HALF_UP, Decimal

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
FIXTURES = os.path.join(ROOT, "tests", "Golden", "fixtures")


def rounded(value):
    """A number as the public pages show it (PHP round() to 1 dp); anything else as it is."""
    try:
        return float(Decimal(repr(float(value))).quantize(Decimal("0.1"), rounding=ROUND_HALF_UP))
    except (TypeError, ValueError):
        return value


def views(fixture):
    """(path, kind, key, expected) for every results view of one fixture."""
    competition = fixture.rsplit("-", 1)[1]
    with open(os.path.join(FIXTURES, fixture, "expected.json")) as f:
        expected = json.load(f)

    for key, event in expected["events"].items():
        event_type, event_id = key.split(":")
        for league, rows in event["results"].items():
            league = league.removeprefix("league:")
            yield f"{competition}/results/{league}/{event_id}-{event_type}", "event", key, rows
    for key, sheet in (expected.get("sheets") or {}).items():
        yield f"{competition}/results/sheet/{key.split(':')[1]}", "sheet", key, sheet["results"]
    for key, master in (expected.get("masters") or {}).items():
        yield f"{competition}/results/sheet/master/{key.split(':')[1]}", "master", key, master["results"]["rows"]


def fetch(base, cache, path, refresh):
    file = os.path.join(cache, path.replace("/", "__") + ".json")
    if refresh or not os.path.exists(file):
        request = urllib.request.Request(
            f"{base}/competition/{path}",
            headers={"Accept": "application/json", "User-Agent": "golden-results-check"},
        )
        with urllib.request.urlopen(request, timeout=60) as response:
            body = response.read()
        with open(file, "wb") as f:
            f.write(body)
    with open(file) as f:
        return json.load(f)["data"]


def compare_event(expected, live):
    want = {r["entity"].split(":")[1]: (rounded(r["points"]), r["position"]) for r in expected}
    disqualified = {r["entity"].split(":")[1] for r in expected if r.get("dqs")}
    got = {}
    for row in live:
        entity, points, position = str(row["id"]), row["data"]["points"], row["data"]["position"]
        if points == "DQ":
            points = want[entity][0] if entity in disqualified else "DQ (not disqualified in the fixture)"
        got[entity] = (rounded(points), position)
    return [f"{e}: expected {want.get(e)}, got {got.get(e)}" for e in sorted(set(want) | set(got)) if want.get(e) != got.get(e)]


def compare_sheet(expected, live):
    def expected_row(r):
        events = sorted(
            (f"{x['event'].split(':')[1]}:{x['event'].split(':')[0]}", x["position"], rounded(x["adjusted"]))
            for x in r["events"]
        )
        return rounded(r["total"]), r["position"], events

    def live_row(r):
        events = sorted((k, v.get("position"), rounded(v.get("points"))) for k, v in r.items() if isinstance(v, dict))
        return rounded(r["points"]), r["position"], events

    return set_difference(list(map(expected_row, expected)), list(map(live_row, live)))


def compare_master(expected, live):
    return set_difference(
        [(r["name"].strip(), rounded(r["total"]), r["position"]) for r in expected],
        [(r["name"].strip(), rounded(r["points"]), r["position"]) for r in live],
    )


def set_difference(want, got):
    want, got = sorted(map(repr, want)), sorted(map(repr, got))
    if want == got:
        return []
    return [f"missing: {r}" for r in want if r not in got][:5] + [f"unexpected: {r}" for r in got if r not in want][:5]


def main():
    parser = argparse.ArgumentParser(description=__doc__.split("\n\n")[0])
    parser.add_argument("--base", default="https://scoring.events", help="site to compare with")
    parser.add_argument("--only", nargs="+", metavar="FIXTURE", help="fixture folder names")
    parser.add_argument("--refresh", action="store_true", help="download again instead of using the cache")
    args = parser.parse_args()

    base = args.base.rstrip("/")
    cache = os.path.join(ROOT, "storage", "app", "live-results", urllib.parse.urlparse(base).netloc.replace(":", "_"))
    os.makedirs(cache, exist_ok=True)

    fixtures = args.only or sorted(os.listdir(FIXTURES))
    checks = [(fixture, *view) for fixture in fixtures for view in views(fixture)]

    def run(check):
        fixture, path, kind, key, expected = check
        try:
            live = fetch(base, cache, path, args.refresh)
        except Exception as error:  # a missing or hidden view counts as a difference
            return fixture, path, [f"could not fetch: {error}"]
        compare = {"event": compare_event, "sheet": compare_sheet, "master": compare_master}[kind]
        return fixture, path, compare(expected, live)

    with concurrent.futures.ThreadPoolExecutor(4) as pool:
        results = list(pool.map(run, checks))

    failed = 0
    for fixture in fixtures:
        mine = [r for r in results if r[0] == fixture]
        bad = [r for r in mine if r[2]]
        failed += len(bad)
        print(f"{fixture:45} {len(mine) - len(bad)}/{len(mine)} views match")
        for _, path, problems in bad:
            print(f"    {path}")
            for problem in problems[:6]:
                print(f"        {problem}")

    print(f"\n{len(results) - failed} of {len(results)} views match {base}")
    return 1 if failed else 0


if __name__ == "__main__":
    sys.exit(main())
