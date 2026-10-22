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

/** What CompetitionLayout draws above every competition page (CompetitionDashboardController::layoutProps) */
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
