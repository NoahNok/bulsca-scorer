import { User } from "./auth"
import { Entity, Event, HeatLane, TankDraw } from "./base"

export type ViolationStatus = "SUBMITTED" | "ACCEPTED" | "REJECTED" | "APPEALED" | "REMOVED"

export const violationStatuses: ViolationStatus[] = [
    "SUBMITTED",
    "ACCEPTED",
    "REJECTED",
    "APPEALED",
    "REMOVED"
]

export function stateColor(submission: ViolationSubmission) {
    if (isVoided(submission.status)) return "text-gray-400";

    return submission.violation.vtype === "DQ"
        ? "text-red-700"
        : "text-orange-700";
}

export function statusTileClass(submission: ViolationSubmission) {
    if (isVoided(submission.status)) return "bg-gray-100 text-gray-400";

    return submission.violation.vtype === "DQ"
        ? "bg-red-100 text-red-700"
        : "bg-orange-100 text-orange-700";
}

export const statusLabels: Record<ViolationStatus, string> = {
    SUBMITTED: "Pending",
    ACCEPTED: "Accepted",
    REJECTED: "Rejected",
    APPEALED: "Appealed",
    REMOVED: "Removed",
}

export function statusBadgeClass(state: ViolationStatus) {
    switch (state) {
        case "SUBMITTED":
            return "bg-se/15 text-teal-700 ring-se/40";
        case "ACCEPTED":
            return "bg-green-100 text-green-700 ring-green-300";
        case "REJECTED":
            return "bg-gray-100 text-gray-600 ring-gray-300";
        case "APPEALED":
            return "bg-amber-100 text-amber-700 ring-amber-300";
        case "REMOVED":
            return "bg-gray-100 text-gray-500 ring-gray-200";
        default:
            return "";
    }
}

// Rejected/removed submissions no longer count against the entity
export function isVoided(state: ViolationStatus) {
    return state === "REJECTED" || state === "REMOVED";
}

export function formatOrder(submission: ViolationSubmission) {
    if ("heat" in submission.order) {
        return `Heat ${submission.order.heat} · Lane ${submission.order.lane}`;
    }

    const tank = submission.order.tank ? `Tank ${submission.order.tank}` : "";
    const draw = `Draw ${submission.order.draw}`;

    return [tank, draw].filter(Boolean).join(" · ");
}

export function submissionCode(submission: ViolationSubmission) {
    return `${submission.violation.vtype === "DQ" ? "DQ" : "P"}${submission.violation.code}`
}

export type ViolationSubmission = {
    id: string
    entity: Entity,
    event: Event,
    violation: Violation
    details: ViolationDetails,
    submitter: ViolationUser,
    seconder: ViolationUser
    status: ViolationStatus
    order: HeatLane | TankDraw
}

type ViolationDetails = {
    turn?: number
    length?: number
    details: string
}

type ViolationUser = {
    name?: string
    position?: string
    user?: User
}

export type ViolationSubmissionPost = {
    entity_id: number
    event?: Pick<Event, "id" | "type">
    violation?: Pick<Violation, "id" | "vtype">
    details: ViolationDetails,
    submitter: ViolationUser,
    seconder: ViolationUser
}

export function emptySubmission(): ViolationSubmissionPost {
    return {
        entity_id: -1,

        event: undefined,
        violation: undefined,

        details: {
            details: ""
        },

        submitter: {
            position: "",
        },

        seconder: {
            name: "",
            position: ""
        }
    }
}



export type Violation = {
    id: number
    code: number
    description: string
    type: "GENERIC" | "LANE" | "TURN" | "CHANGEOVER" | "CROSSLINE" | "BACKLINE" | "OOF" | "STARTER" | "PICKUP" | null
    vtype: "DQ" | "PEN"
}
