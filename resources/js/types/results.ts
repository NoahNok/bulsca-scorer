import type { Entity } from "./base"
import type { ViolationStatus } from "./violation"

// Shapes sent by ConfirmResultsController for the head ref's Confirm Results pages

export type ResultViolation = {
    code: number
    label: string // DQ12, P3, DNF...
    message: string
}

export type PendingViolation = {
    id: string
    label: string
    status: ViolationStatus
    message: string | null
}

export type EntityViolations = {
    dqs: ResultViolation[]
    penalties: ResultViolation[]
    pending: PendingViolation[]
}

export type ConfirmSummary = {
    entities: number
    missing: number
    pending: number
    missingOof?: number
}

export type ConfirmLane = { lane: number } & (
    | { entity: null }
    | ({
        entity: Entity
        oof: number | null
        result: string | null
        resultIsDq: boolean
    } & EntityViolations)
)

export type ConfirmHeat = {
    heat: number
    lanes: ConfirmLane[]
}

export type ConfirmJudge = {
    id: number
    name: string
    markingPoints: { id: number; name: string; weight: number }[]
}

export type ConfirmSercEntity = {
    tank: number | null
    draw: number
    entity: Entity | null
    marks: Record<number, number | null>
    judgeTotals: Record<number, number>
    total: number
    missing: number
    notes: { judge: string | null; note: string }[]
} & EntityViolations

export type ScrollData<T> = {
    data: T[]
}
