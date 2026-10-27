/** A competition as the Management home lists it (ManageController::row) */
export type ManagedCompetition = {
    id: number;
    name: string;
    /** Y-m-d */
    when: string;
    where: string;
    url: string;
    past: boolean;
    /** Days from today, negative once it's past */
    days: number;
    today: boolean;
    /** Today, with the live site turned on */
    live: boolean;
    /** Hidden from the public site */
    private: boolean;
    /** An Organiser with only some permissions, e.g. results */
    limited: boolean;
};

/** One of the Management home's list, with its organisation (null for a Personal Competition) */
export type ListedCompetition = ManagedCompetition & { organisation: number | null };

export type ManagedOrganisation = {
    id: number;
    name: string;
    logo: string | null;
    /** Null for one you were only invited into a competition of: you can't open its page */
    url: string | null;
};

/** A competition page's tab (App\Helpers\CompetitionTabs), only the ones you can open */
export type CompetitionTab = {
    label: string;
    url: string;
    current: boolean;
    /** A Management app page, so an Inertia visit; the rest are Blade, and load in full */
    svelte: boolean;
};

/** What CompetitionLayout draws above every competition page (App\Helpers\CompetitionHeader::for) */
export type CompetitionHeader = {
    id: number;
    name: string;
    /** Y-m-d */
    when: string;
    where: string;
    organisation: { name: string; logo: string | null; url: string } | null;
    championship: { name: string; url: string } | null;
    /** Hidden from the public site */
    private: boolean;
    /** Today, with the live site turned on */
    live: boolean;
    /** The Organisers' note above every page, as HTML from the Status settings */
    statusMessage: string | null;
    tabs: CompetitionTab[];
};

/** A speed event (key `sp:<id>`) or SERC (`se:<id>`) */
export type CompetitionEvent = {
    key: string;
    name: string;
    serc: boolean;
};

/** Someone who can manage a competition, on its Settings page */
export type CompetitionAccount = {
    id: number;
    name: string;
    email: string;
    /** Keys of Competition::$accessTypes */
    access: string[];
    owner: boolean;
    you: boolean;
};

/**
 * Where an official is with Digital Judge: their account not signed in to yet (made by an Organiser),
 * the competition not opened yet, using it now, or last seen a while ago
 */
export type OfficialStatus = "notSignedIn" | "notOpened" | "online" | "seen";

/** An official on a competition's Officials page (Manage\CompetitionOfficialsController::index) */
export type CompetitionOfficial = {
    id: number;
    name: string;
    email: string;
    /** A key of Competition::$officialRoles */
    role: string;
    status: OfficialStatus;
    joined: string | null;
    lastSeen: string | null;
    /** The competition's only Referee, so changing or removing them leaves none */
    onlyReferee: boolean;
    /** The confirm message for removing them, saying what they leave behind */
    removeMessage: string;
};

/** Someone to add as an official: a search result, or a suggestion from the organisation's other competitions */
export type OfficialCandidate = {
    id: number;
    name: string;
    /** Masked, unless searched for in full */
    email: string;
    /** Search results: already an official here */
    official?: boolean;
    /** Suggestions: the competition they last officiated */
    last?: string;
};

/** A team or competitor on Entries & Leagues (CompetitionEntriesController::index) */
export type Entry = {
    id: number;
    name: string;
    /** League ids */
    leagues: number[];
    /** Seed time (01:23.456) by speed event id, only for events that take seeds */
    seeds: Record<string, string>;
};

export type EntryTeam = Entry & { competitors: Entry[] };

export type EntryClub = { id: number; name: string; teams: EntryTeam[] };

/** A competition's league, with how many teams and competitors are in it */
export type CompetitionLeague = { id: number; name: string; teams: number; competitors: number };

/** An event's card on the Events page (CompetitionEventsController::index) */
type EventCard = {
    id: number;
    name: string;
    /** Marked complete by an Organiser */
    complete: boolean;
    /** On the Digital Judge, and confirmed by the Referee */
    confirmed: boolean;
    /** Left out of the results */
    hidden: boolean;
    url: string;
};

/** The Entities a speed event or SERC scores */
export type EventTarget = "club" | "team" | "competitor";

export type SpeedEventCard = EventCard & { target: EventTarget };

/** A speed event, as each of its pages takes it */
export type SpeedEventHeader = {
    id: number;
    name: string;
    target: EventTarget;
    complete: boolean;
    viewable: boolean;
    hasPenalties: boolean;
    digitalJudge: boolean;
    ropeThrow: boolean;
    /** How many heats it has, which deleting it removes */
    heats: number;
};

export type SercType = "DRY" | "WET";

export type SercCard = EventCard & { type: SercType };

/** A SERC, as each of its pages takes it (Manage\SercEventController::shared) */
export type SercEventHeader = {
    id: number;
    name: string;
    type: SercType;
    target: EventTarget;
    complete: boolean;
    viewable: boolean;
    /** Each Entity is marked against the Objective its League is assigned */
    leagueObjectives: boolean;
    /** SERC access; a SERC Writer only sees its results and edits its setup */
    canManage: boolean;
};

/** An Entity a SERC scores, in draw order, with how many of its marks are in */
export type SercEntity = {
    key: string;
    id: number;
    name: string;
    /** Its place in the draw (Tank 2-3 with tanks), null when it isn't in one */
    draw: string | null;
    marked: number;
    /** How many marks it takes; null when League Objectives give it none or several Objectives */
    possible: number | null;
};

/** A ranked row on a SERC's page. A combined row stands for several Entities, one per entry in `names` */
export type SercResultRow = {
    key: string;
    names: string[];
    dq: string | null;
    /** The weighted mark */
    mark: number;
    points: number;
    disqualified: boolean;
    position: number | null;
};

/** A marking point as the setup editor edits it; id null until it's saved */
export type SercMarkingPoint = {
    id: number | null;
    description: string;
    weight: number | null;
    template_id: string | null;
    /** Keys a new one in the list until it has an id */
    sort_id?: number;
};

/** An Objective as the setup editor edits it (SERC::getSERCConfiguration) */
export type SercObjective = {
    id: number | null;
    name: string;
    /** Its marking hint, as HTML */
    hint: string | null;
    /** League ids, with League Objectives on */
    restricted_leagues: number[];
    marking_points: SercMarkingPoint[];
    sort_id?: number;
};

/** An Objective on an Entity's marks page, with its marks (null for none) */
export type SercMarkingObjective = {
    id: number;
    name: string;
    /** Its marking hint, as HTML */
    hint: string | null;
    markingPoints: { id: number; name: string; weight: number; mark: number | null }[];
};

/** What the setup editor starts from */
export type SercSetup = {
    name: string;
    type: SercType;
    target: EventTarget;
    leagueObjectives: boolean;
    objectives: SercObjective[];
};

/** One speed result as the Enter results grid edits it (Manage\SpeedEventController::grid) */
export type SpeedGridRow = {
    id: number;
    name: string;
    /** The pool its heat swims in, when the competition uses pools */
    pool: { id: number; name: string } | null;
    heat: number | null;
    /** The heat's pool and number ("12:3"), as heat numbers restart in each pool */
    heatKey: string | null;
    /** "H3 L2", or "Main Pool · H3 L2" with a pool */
    heatLane: string | null;
    lane: number | null;
    /** A time (01:23.450), DNS, DNF or OOT, Rope Throw's ropes in (0-3), or empty */
    result: string;
    /** DQ101, or empty */
    disqualification: string;
    /** P100, P200, or empty */
    penalties: string;
};

/** A ranked row on a speed event's Results. A combined row stands for several Entities, one per entry in `names` */
export type SpeedResultRow = {
    key: string;
    names: string[];
    /** Each combined Entity's own time; empty for a single one */
    times: string[];
    /** Each Entity's DQ, null for none */
    dqs: (string | null)[];
    /** The time that counts, after penalties */
    time: string | null;
    /** The time entered, when penalties changed it */
    was: string | null;
    /** Heat, lane and order of finish, with the Digital Judge on */
    oof: string | null;
    penalties: string | null;
    /** "DQ" when disqualified ones don't score */
    points: number | "DQ" | null;
    position: number | null;
};

export type ScoringVariable = { order?: number; name: string; expression: string };

export type ScoringViolation = { code: string; condition: string; amount: string };

/** An event's scoring schema (ScoringSchemas::withDefaults) */
export type ScoringSchema = {
    equation: string;
    penalty_func: string | null;
    global_variables: ScoringVariable[];
    local_variables: ScoringVariable[];
    auto_penalties: ScoringViolation[];
    auto_disqualifications: ScoringViolation[];
    rank_higher: boolean;
    rank_equation: string | null;
    allow_disqualified_to_rank: boolean;
    group_by: string[];
    force_penalty: boolean;
};

/** What an event page's Scoring part edits */
export type EventScoring = {
    /** Null until it's set up */
    schema: ScoringSchema | null;
    /** Organisation schemas to start from, with their organisation's name */
    options: { id: number; name: string; organisation: string; schema: ScoringSchema }[];
};
