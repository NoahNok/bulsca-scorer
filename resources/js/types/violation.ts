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

export function stateColor(state: ViolationStatus) {
    switch (state) {
        case "SUBMITTED":
            return "text-se";
        case "ACCEPTED":
            return "text-green-500";
        case "REJECTED":
            return "text-red-500";
        case "APPEALED":
            return "text-orange-500";
        case "REMOVED":
            return "text-red-500";
        default:
            return "";
    }
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
