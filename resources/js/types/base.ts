

export type Competition = {
    id: number;
    name: string;
    where?: string
    when?: string

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
    marking_points?: MarkingPoint[]
    no_marking_points?: number
}


export type MarkingPoint = {
    id: number
    description: string
}

export type SERC = Event & {
    judges: Judge[]
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