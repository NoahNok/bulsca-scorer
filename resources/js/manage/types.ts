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
