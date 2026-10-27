import type { Heat, Pool } from "@/types/base";

/**
 * How a heat is named wherever it's shown: "Main Pool · Heat 3", or "Heat 3" without pools.
 */
export function heatLabel(heat: { heat: number; pool?: Pool | null }): string {
    return heat.pool ? `${heat.pool.name} · Heat ${heat.heat}` : `Heat ${heat.heat}`;
}

/**
 * Identifies a heat as "pool:heat", with 0 for no pool, matching Heat::keyFor.
 */
export function heatKey(heat: { heat: number; pool?: Pool | null }): string {
    return `${heat.pool?.id ?? 0}:${heat.heat}`;
}

/**
 * Heat numbers restart in each pool, so routes to a heat carry its pool as ?pool=.
 */
export function heatQuery(heat: Pick<Heat, "pool">) {
    return heat.pool ? { query: { pool: heat.pool.id } } : undefined;
}

/**
 * Lanes to show for a heat: its pool's lanes, or the competition's lane count without pools.
 */
export function laneCount(heat: Pick<Heat, "pool">, maxLanes: number): number {
    return heat.pool?.lanes ?? maxLanes;
}
