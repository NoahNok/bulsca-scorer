

export type Competition = {
    id: number;
    name: string;
    where?: string
    when?: string

    show_teams_to_judges?: boolean


}


export enum EventType {
    SPEED = "speed",
    SERC = "serc"
}

export type Event = {
    id: number
    name: string
    type: EventType
    completed: boolean
    confirmed: boolean
}

export type Judge = {
    id: number
    name: string
    marking_points: MarkingPoint[]
    no_marking_points?: number
    description?: string
}


export type MarkingPoint = {
    id: number
    description: string
    template?: MarkingPointTemplate
    stats?: {
        min: number
        max: number
        avg: number
    }
}



export type MarkingPointTemplate = {
    mode: 'default' | 'choice'
    min: number
    max: number
    step: number
    use_toggle_for_half: boolean
    choice: MarkingPointChoice[]
}

export type MarkingPointChoice = {
    value: number
    label: string
}

export type SERC = Event & {
    judges: Judge[]

}

export type SpeedEvent = Event & {
    max_lanes: number
}

export type Entity = {
    name: string
    id: number
    type: 'club' | 'team' | 'competitior'
}

export type Draw = {
    draw: number
    entity: Entity
}

export type Mark = {
    marking_point: MarkingPoint
    mark: number | null
}

export type EntityMark = {
    mark: number | null
    entity: Entity
}

export type JudgeMarks = {
    judge: Judge
    marks: Mark[]
    notes?: string
}

export type PreviousMarks = {
    marking_point: MarkingPoint,
    marks: EntityMark[]
}

export type JudgeNotes = {
    name: string
    notes: Note[]
}

export type Note = {
    entity: Entity
    note: string
}

export type CurrentDraw = {
    text: string
    percent: number
    percent_unit: number
    index: number
    total: number
}

export type Heat = {
    heat: number
    complete: boolean
    lanes?: Lane[]
}

export type Lane = {
    lane: number
    entity: Entity
}

export type OOFLane = Lane & {
    oof?: number
}

export type OOFHeat = Omit<Heat, 'lanes'> & {
    lanes?: OOFLane[]
}