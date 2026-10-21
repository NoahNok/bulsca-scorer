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

export type ManagedOrganisation = {
    name: string;
    logo: string | null;
    url: string;
    /** Upcoming, then the latest past ones */
    competitions: ManagedCompetition[];
    /** It has older competitions than these */
    more: boolean;
};
