<!--
    A competition's Officials (manage.competitions.officials), in CompetitionLayout: everyone who can judge
    it on Digital Judge, with their role and whether they've signed in, and beside them how officials join
    and copying them from another competition. A row opens a dialog to change their role, email them again
    or remove them, and Add official opens one to find or invite someone (Manage\CompetitionOfficialsController).
-->
<script lang="ts">
    import {
        copy,
        destroy,
        resend,
        search,
        store,
        update,
    } from "@/actions/App/Http/Controllers/Manage/CompetitionOfficialsController";
    import { confirm } from "@/lib/confirm";
    import { timeAgo } from "@/lib/timeAgo.svelte";
    import Alert from "@/manage/components/Alert.svelte";
    import Field from "@/manage/components/Field.svelte";
    import SidebarPage from "@/manage/components/SidebarPage.svelte";
    import RowIcon from "@/manage/components/RowIcon.svelte";
    import EmptyState from "@/manage/components/EmptyState.svelte";
    import type {
        CompetitionHeader,
        CompetitionOfficial,
        OfficialCandidate,
        OfficialStatus,
    } from "@/manage/types";
    import { OfficialsPresence } from "@/manage/officialsPresence.svelte";
    import { closeOnBackdrop } from "@/manage/ui";
    import { Form, router } from "@inertiajs/svelte";
    import { tick } from "svelte";

    let {
        competition,
        officials,
        roles,
        suggestions,
        previousCompetitions,
        digitalJudge,
    }: {
        competition: CompetitionHeader;
        officials: CompetitionOfficial[];
        /** Competition::$officialRoles: key to name */
        roles: Record<string, string>;
        /** People from the organisation's other competitions, shown in the add dialog before anything's typed */
        suggestions: OfficialCandidate[];
        /** The organisation's other competitions with officials, most recent first */
        previousCompetitions: {
            id: number;
            name: string;
            when: string | null;
            officials: number;
        }[];
        digitalJudge: {
            enabled: boolean;
            pin: string | null;
            judgeUrl: string;
            qrUrl: string;
            overviewUrl: string;
        };
    } = $props();

    // Who's on Digital Judge now, from its presence channel. Without a connection (no Reverb, or
    // still connecting), the server's status stands, which counts anyone seen in the last few minutes
    const presence = new OfficialsPresence();

    $effect(() => {
        presence.join(competition.id);
        return () => presence.leave();
    });

    type Status = OfficialStatus | "idle";

    function statusOf(official: CompetitionOfficial): Status {
        if (!presence.connected) return official.status;
        const live = presence.statuses.get(official.id);
        if (live) return live === "idle" ? "idle" : "online";
        // Not in the channel: the server's "online" only means seen in the last few minutes
        return official.status === "online" ? "seen" : official.status;
    }

    const isOnline = (status: Status) => status === "online" || status === "idle";
    const joinedYet = (status: Status) => isOnline(status) || status === "seen";

    const counts = $derived({
        all: officials.length,
        referees: officials.filter((o) => o.role === "referee").length,
        online: officials.filter((o) => isOnline(statusOf(o))).length,
        notJoined: officials.filter((o) => !joinedYet(statusOf(o))).length,
    });

    type Filter = keyof typeof counts;

    const FILTERS: { key: Filter; label: string }[] = [
        { key: "all", label: "All" },
        { key: "referees", label: "Referees" },
        { key: "online", label: "Online now" },
        { key: "notJoined", label: "Not joined" },
    ];

    let filter = $state<Filter>("all");
    let query = $state("");

    const shown = $derived.by(() => {
        const term = query.trim().toLowerCase();
        return officials.filter(
            (o) =>
                (filter === "all" ||
                    (filter === "referees" && o.role === "referee") ||
                    (filter === "online" && isOnline(statusOf(o))) ||
                    (filter === "notJoined" && !joinedYet(statusOf(o)))) &&
                (!term ||
                    o.name.toLowerCase().includes(term) ||
                    o.email.toLowerCase().includes(term)),
        );
    });

    const judgeHost = $derived(new URL(digitalJudge.judgeUrl).host);

    function statusText(official: CompetitionOfficial) {
        const status = statusOf(official);
        if (status === "notSignedIn") return "Hasn't signed in yet";
        if (status === "notOpened") return "Hasn't opened it yet";
        if (status === "online") return "Online now";
        if (status === "idle") return "Online, idle";
        return `Seen ${timeAgo(official.lastSeen!)}`;
    }

    function day(iso: string) {
        return new Date(iso).toLocaleDateString("en-GB", {
            day: "numeric",
            month: "short",
            year: "numeric",
        });
    }

    function plural(count: number, word: string) {
        return `${count} ${word}${count === 1 ? "" : "s"}`;
    }

    // The heading and Add button stick to the top of the screen over a long table, with a rule under
    // them only once they're stuck: a marker just above them has scrolled off the top
    let stuck = $state(false);

    function watchStuck(marker: HTMLElement) {
        const observer = new IntersectionObserver(
            ([entry]) =>
                (stuck =
                    !entry.isIntersecting && entry.boundingClientRect.top < 0),
        );
        observer.observe(marker);
        return () => observer.disconnect();
    }

    // Remounts a dialog's form each time it opens, so it starts empty or from saved values
    let opened = $state(0);

    // Add official: search accounts by name or email, pick one or a suggestion, or invite a new email

    let addDialog: HTMLDialogElement;
    let searchInput = $state<HTMLInputElement>();
    let lookup = $state("");
    let results = $state<OfficialCandidate[]>([]);
    let exactEmail = $state(false);
    /** The query the results are for, so a slow answer to an older query is dropped */
    let searched = $state("");
    let picked = $state<OfficialCandidate | null>(null);
    let searchTimer: ReturnType<typeof setTimeout> | undefined;

    const isEmail = $derived(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(lookup.trim()));
    // The name field is only for making a new account
    const isNewAccount = $derived(
        !picked && isEmail && searched === lookup.trim() && !exactEmail,
    );

    async function openAdd() {
        lookup = "";
        results = [];
        searched = "";
        exactEmail = false;
        picked = null;
        opened++;
        await tick();
        addDialog.showModal();
    }

    function onLookup() {
        clearTimeout(searchTimer);
        const q = lookup.trim();
        if (q.length < 2) {
            results = [];
            searched = "";
            return;
        }
        searchTimer = setTimeout(async () => {
            const response = await fetch(
                search.url(competition.id, { query: { q } }),
                { headers: { Accept: "application/json" } },
            );
            const data: { results: OfficialCandidate[]; exactEmail: boolean } =
                await response.json();
            if (q !== lookup.trim()) return;
            results = data.results;
            exactEmail = data.exactEmail;
            searched = q;
        }, 250);
    }

    function pick(candidate: OfficialCandidate) {
        if (candidate.official) return;
        picked = candidate;
        results = [];
    }

    async function unpick() {
        picked = null;
        await tick();
        searchInput?.focus();
    }

    // An official: change their role, email them again or remove them

    let officialDialog: HTMLDialogElement;
    let current = $state<CompetitionOfficial | null>(null);
    let role = $state("official");
    let saving = $state(false);
    let dialogErrors = $state<Record<string, string>>({});

    async function openOfficial(official: CompetitionOfficial) {
        current = official;
        role = official.role;
        dialogErrors = {};
        await tick();
        officialDialog.showModal();
    }

    async function saveRole(official: CompetitionOfficial) {
        if (official.onlyReferee && role !== "referee") {
            const ok = await confirm({
                title: `Make ${official.name} ${role === "official" ? "an official" : roles[role]}?`,
                description:
                    "They're the only referee, so no one will be able to review DQs and penalties or confirm results.",
                confirmLabel: "Change role",
                confirmVariant: "danger",
            });
            if (!ok) return;
        }

        router.post(
            update({ comp: competition.id, official: official.id }).url,
            { role },
            {
                preserveScroll: true,
                onStart: () => (saving = true),
                onFinish: () => (saving = false),
                onSuccess: () => officialDialog.close(),
                onError: (errors) => (dialogErrors = errors),
            },
        );
    }

    function resendEmail(official: CompetitionOfficial) {
        router.post(
            resend({ comp: competition.id, official: official.id }).url,
            {},
            {
                preserveScroll: true,
                onStart: () => (saving = true),
                onFinish: () => (saving = false),
                onSuccess: () => officialDialog.close(),
                onError: (errors) => (dialogErrors = errors),
            },
        );
    }

    async function remove(official: CompetitionOfficial) {
        const ok = await confirm({
            title: `Remove ${official.name}?`,
            description: official.removeMessage,
            confirmLabel: "Remove",
            confirmVariant: "danger",
        });
        if (!ok) return;

        router.delete(
            destroy({ comp: competition.id, official: official.id }).url,
            {
                preserveScroll: true,
                onSuccess: () => officialDialog.close(),
            },
        );
    }

    const heading = "text-lg font-extrabold uppercase";
    // A list row that's a button: the whole row is the target
    const listRow =
        "flex w-full cursor-pointer items-center gap-3 px-2 py-[0.6rem] text-left transition-colors hover:bg-v2-row-hover focus-visible:bg-v2-row-hover focus-visible:outline-none disabled:cursor-not-allowed disabled:text-v2-muted disabled:hover:bg-transparent motion-reduce:transition-none";
</script>

<svelte:head>
    <title>Officials - {competition.name} - Scoring.Events</title>
</svelte:head>

{#snippet status(official: CompetitionOfficial)}
    {@const now = statusOf(official)}
    {#if now === "online"}
        <span
            class="inline-flex items-center gap-2 text-sm font-semibold whitespace-nowrap text-v2-teal-text"
        >
            <span class="v2-dot is-live relative -top-px" aria-hidden="true"></span>Online now
        </span>
    {:else if now === "idle"}
        <span
            class="inline-flex items-center gap-2 text-sm font-semibold whitespace-nowrap text-v2-muted"
        >
            <span class="v2-dot relative -top-px bg-[#fb923c]" aria-hidden="true"></span>Online, idle
        </span>
    {:else if now === "seen"}
        <span class="text-sm whitespace-nowrap text-v2-muted"
            >{statusText(official)}</span
        >
    {:else}
        <span class="v2-badge is-provisional whitespace-nowrap"
            >{statusText(official)}</span
        >
    {/if}
{/snippet}

{#snippet candidates(list: OfficialCandidate[])}
    <ul class="border-t border-v2-border *:border-b *:border-v2-border">
        {#each list as candidate (candidate.id)}
            <li>
                <button
                    type="button"
                    class={listRow}
                    disabled={candidate.official}
                    onclick={() => pick(candidate)}
                >
                    <span class="min-w-0">
                        <span class="block font-semibold wrap-anywhere"
                            >{candidate.name}</span
                        >
                        <span class="block text-sm text-v2-muted wrap-anywhere"
                            >{candidate.email}</span
                        >
                    </span>
                    {#if candidate.official}
                        <span class="ml-auto shrink-0 text-[0.8125rem]"
                            >Already an official</span
                        >
                    {:else if candidate.last}
                        <span
                            class="ml-auto max-w-[40%] shrink-0 truncate text-[0.8125rem] text-v2-muted"
                            >Last: {candidate.last}</span
                        >
                    {/if}
                </button>
            </li>
        {/each}
    </ul>
{/snippet}

<SidebarPage>
    <section aria-labelledby="o-officials">
        <div {@attach watchStuck}></div>
        <div
            class={[
                "sticky top-0 z-10 -mx-2 flex flex-wrap items-center justify-between gap-x-4 gap-y-3 border-b bg-v2-bg px-2 py-2",
                stuck ? "border-v2-border" : "border-transparent",
            ]}
        >
            <div>
                <h2 id="o-officials" class={heading}>Officials</h2>
                <p class="mt-0.5 text-sm text-v2-muted">
                    {plural(counts.all, "official")} · {plural(
                        counts.referees,
                        "referee",
                    )} · {counts.online} online now
                </p>
            </div>
            <button
                type="button"
                class="v2-btn v2-btn-sm v2-btn-primary"
                onclick={openAdd}>Add official</button
            >
            {#if officials.length > 0}
                <div
                    class="flex basis-full flex-wrap gap-2"
                    role="group"
                    aria-label="Show"
                >
                    {#each FILTERS as option (option.key)}
                        <button
                            type="button"
                            class="v2-chip"
                            aria-pressed={filter === option.key}
                            onclick={() => (filter = option.key)}
                        >
                            {option.label}
                            <span class="tabular-nums opacity-70"
                                >{counts[option.key]}</span
                            >
                        </button>
                    {/each}
                </div>
                <input
                    type="search"
                    class="v2-input w-full basis-full"
                    placeholder="Search by name or email"
                    aria-label="Search officials"
                    bind:value={query}
                />
            {/if}
        </div>

        {#if counts.referees === 0}
            <div class="mt-5">
                <Alert tone="danger" title="No referee yet">
                    Only referees can review DQs and penalties, release SERCs
                    and confirm results on Digital Judge. Give at least one
                    official the Referee role.
                </Alert>
            </div>
        {/if}

        {#if officials.length > 0}
            <div class="mt-5 overflow-x-auto">
                <table class="w-full border-t border-v2-border text-left">
                    <thead>
                        <tr
                            class="text-[0.8125rem] font-semibold text-v2-muted *:border-b *:border-v2-border *:px-2 *:py-2 *:font-semibold"
                        >
                            <th scope="col">Official</th>
                            <th scope="col">Role</th>
                            <th scope="col" class="max-sm:hidden">Status</th>
                            <th scope="col"
                                ><span class="sr-only">Edit</span></th
                            >
                        </tr>
                    </thead>
                    <tbody>
                        {#each shown as official (official.id)}
                            <tr
                                class="v2-row *:border-b *:border-v2-border *:px-2 *:py-3 *:align-middle"
                            >
                                <td class="min-w-0">
                                    <button
                                        type="button"
                                        class="v2-row-link flex cursor-pointer flex-col items-start text-left focus-visible:outline-none"
                                        onclick={() => openOfficial(official)}
                                    >
                                        <span class="font-bold wrap-anywhere"
                                            >{official.name}</span
                                        >
                                        <span
                                            class="text-sm text-v2-muted wrap-anywhere"
                                            >{official.email}</span
                                        >
                                        <span class="sr-only">Edit</span>
                                        <span class="mt-1 sm:hidden"
                                            >{@render status(official)}</span
                                        >
                                    </button>
                                </td>
                                <td class="whitespace-nowrap">
                                    <span
                                        class={[
                                            "v2-badge",
                                            official.role === "referee" &&
                                                "is-live",
                                        ]}
                                        >{roles[official.role] ??
                                            official.role}</span
                                    >
                                </td>
                                <td class="max-sm:hidden"
                                    >{@render status(official)}</td
                                >
                                <td class="w-px"><RowIcon /></td>
                            </tr>
                        {/each}
                    </tbody>
                </table>
                {#if shown.length === 0}
                    <p
                        class="px-2 py-6 text-center text-[0.9375rem] text-v2-muted"
                    >
                        {query.trim()
                            ? `No officials match “${query.trim()}”.`
                            : "No officials here."}
                    </p>
                {/if}
            </div>
        {:else}
            <EmptyState class="mt-5">No officials yet. Add one, or share the Digital Judge PIN.</EmptyState>
        {/if}
    </section>

    {#snippet sidebar()}
        <div class="flex flex-col gap-10">
            <section aria-labelledby="o-join">
                <h2 id="o-join" class={heading}>How officials join</h2>
                {#if digitalJudge.enabled}
                    <p
                        class="mt-4 text-[0.8125rem] font-semibold text-v2-muted"
                    >
                        PIN
                    </p>
                    <p
                        class="font-anton text-[clamp(2.75rem,7vw,3.5rem)] leading-none tracking-[0.08em] text-v2-teal-text tabular-nums select-all dark:text-v2-teal"
                    >
                        {digitalJudge.pin}
                    </p>
                    <p class="mt-3 text-sm/[1.6] text-v2-muted">
                        They sign in at <a
                            href={digitalJudge.judgeUrl}
                            target="_blank"
                            rel="noopener"
                            class="v2-link-inline">{judgeHost}</a
                        >
                        and enter it, or scan the
                        <a
                            href={digitalJudge.qrUrl}
                            target="_blank"
                            rel="noopener"
                            class="v2-link-inline">printable QR code</a
                        >. People you add here don't need it.
                    </p>
                {:else}
                    <p class="mt-3 text-sm/[1.6] text-v2-muted">
                        Officials can join with the Digital Judge PIN once it's
                        on. Turn it on from the
                        <a
                            href={digitalJudge.overviewUrl}
                            class="v2-link-inline">Overview</a
                        >. You can add people here before then.
                    </p>
                {/if}
            </section>

            {#if previousCompetitions.length > 0}
                <section aria-labelledby="o-copy">
                    <h2 id="o-copy" class={heading}>
                        Copy from another competition
                    </h2>
                    <Form
                        action={copy(competition.id)}
                        options={{ preserveScroll: true }}
                    >
                        {#snippet children({ errors, processing })}
                            <div class="v2-field mt-4">
                                <label for="o-copy-from" class="sr-only"
                                    >Competition</label
                                >
                                <div class="flex items-center gap-2">
                                    <select
                                        id="o-copy-from"
                                        name="from"
                                        class={[
                                            "v2-input min-w-0 flex-1",
                                            errors.from && "is-invalid",
                                        ]}
                                        aria-invalid={errors.from
                                            ? "true"
                                            : undefined}
                                        aria-describedby={errors.from
                                            ? "o-copy-error"
                                            : "o-copy-hint"}
                                    >
                                        {#each previousCompetitions as previous (previous.id)}
                                            <option value={previous.id}>
                                                {previous.name}{previous.when
                                                    ? `, ${day(previous.when)}`
                                                    : ""} · {plural(
                                                    previous.officials,
                                                    "official",
                                                )}
                                            </option>
                                        {/each}
                                    </select>
                                    <button
                                        type="submit"
                                        class="v2-btn shrink-0"
                                        disabled={processing}>Add all</button
                                    >
                                </div>
                                {#if errors.from}<span
                                        id="o-copy-error"
                                        class="v2-error">{errors.from}</span
                                    >{/if}
                                <span id="o-copy-hint" class="v2-hint"
                                    >They keep the role they had there, and we
                                    email anyone who's new.</span
                                >
                            </div>
                        {/snippet}
                    </Form>
                </section>
            {/if}
        </div>
    {/snippet}
</SidebarPage>

<dialog
    bind:this={addDialog}
    class="v2-modal"
    aria-labelledby="o-add-title"
    onclick={closeOnBackdrop}
>
    <h2 id="o-add-title">Add official</h2>
    <p>
        They'll get an email and see the competition on their Digital Judge home
        page.
    </p>

    {#key opened}
        <Form
            action={store(competition.id)}
            options={{ preserveScroll: true }}
            onSuccess={() => addDialog.close()}
        >
            {#snippet children({ errors, processing })}
                {@const lookupError = errors.email ?? errors.user_id}
                <div class="v2-field mt-5">
                    <label for="o-lookup">Official</label>
                    {#if picked}
                        <div
                            class="flex items-center gap-3 rounded-lg border border-v2-border bg-v2-card px-3 py-2"
                        >
                            <span class="min-w-0 flex-1">
                                <span class="block font-semibold wrap-anywhere"
                                    >{picked.name}</span
                                >
                                <span
                                    class="block text-sm text-v2-muted wrap-anywhere"
                                    >{picked.email}</span
                                >
                            </span>
                            <button
                                type="button"
                                id="o-lookup"
                                class="v2-btn v2-btn-sm shrink-0"
                                onclick={unpick}>Change</button
                            >
                            <input
                                type="hidden"
                                name="user_id"
                                value={picked.id}
                            />
                        </div>
                    {:else}
                        <input
                            bind:this={searchInput}
                            bind:value={lookup}
                            oninput={onLookup}
                            type="search"
                            id="o-lookup"
                            name="email"
                            class={["v2-input", lookupError && "is-invalid"]}
                            placeholder="Search by name or email"
                            autocomplete="off"
                            aria-invalid={lookupError ? "true" : undefined}
                            aria-describedby={lookupError
                                ? "o-lookup-error"
                                : undefined}
                        />
                    {/if}
                    {#if lookupError}<span id="o-lookup-error" class="v2-error"
                            >{lookupError}</span
                        >{/if}
                </div>

                {#if !picked}
                    {#if results.length > 0}
                        <div class="mt-3">{@render candidates(results)}</div>
                    {:else if lookup.trim() === "" && suggestions.length > 0}
                        <p
                            class="mt-4 mb-1 text-sm font-semibold text-v2-muted"
                        >
                            From your other competitions
                        </p>
                        {@render candidates(suggestions)}
                    {:else if !isEmail && searched && searched === lookup.trim()}
                        <p class="mt-2 text-sm text-v2-muted">
                            No one found. To add someone new, enter their email
                            address.
                        </p>
                    {/if}
                {/if}

                {#if isNewAccount}
                    <div class="mt-4">
                        <Field
                            id="o-name"
                            name="name"
                            label="Name"
                            required
                            autocomplete="off"
                            hint="No account uses this email yet, so we'll make one. They sign in with a PIN we email them."
                            error={errors.name}
                        />
                    </div>
                {:else if errors.name}
                    <span class="v2-error">{errors.name}</span>
                {/if}

                <div class="v2-field">
                    <label for="o-role">Role</label>
                    <select
                        id="o-role"
                        name="role"
                        class="v2-input w-full"
                        value="official"
                    >
                        {#each Object.entries(roles) as [key, label] (key)}
                            <option value={key}>{label}</option>
                        {/each}
                    </select>
                    {#if errors.role}<span class="v2-error">{errors.role}</span
                        >{/if}
                </div>

                <div class="v2-modal-actions mt-6">
                    <button
                        type="button"
                        class="v2-btn"
                        onclick={() => addDialog.close()}>Cancel</button
                    >
                    <button
                        type="submit"
                        class="v2-btn v2-btn-primary"
                        disabled={processing}>Add official</button
                    >
                </div>
            {/snippet}
        </Form>
    {/key}
</dialog>

<dialog
    bind:this={officialDialog}
    class="v2-modal"
    aria-labelledby="o-official-title"
    onclick={closeOnBackdrop}
>
    {#if current}
        {@const official = current}
        <h2 id="o-official-title">{official.name}</h2>
        <p>{official.email}</p>
        <p
            class="mt-3 flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-v2-muted"
        >
            {#if official.joined}<span>Added {day(official.joined)}</span><span
                    aria-hidden="true">·</span
                >{/if}
            {@render status(official)}
        </p>

        <div class="v2-field mt-5">
            <label for="o-official-role">Role</label>
            <select
                id="o-official-role"
                class="v2-input w-full"
                bind:value={role}
            >
                {#each Object.entries(roles) as [key, label] (key)}
                    <option value={key}>{label}</option>
                {/each}
            </select>
            {#if dialogErrors.role}<span class="v2-error"
                    >{dialogErrors.role}</span
                >{/if}
            <span class="v2-hint"
                >Referees review DQs and penalties, release SERCs and confirm
                results.</span
            >
        </div>

        {#if !joinedYet(statusOf(official))}
            <div class="mt-5 flex flex-col items-start gap-2">
                <button
                    type="button"
                    class="v2-btn v2-btn-sm"
                    disabled={saving}
                    onclick={() => resendEmail(official)}>Resend email</button
                >
                {#if dialogErrors.resend}<span class="v2-error"
                        >{dialogErrors.resend}</span
                    >{/if}
            </div>
        {/if}

        <div class="v2-modal-actions mt-6">
            <button
                type="button"
                class="v2-btn v2-btn-ghost mr-auto text-v2-danger-text"
                onclick={() => remove(official)}>Remove</button
            >
            <button
                type="button"
                class="v2-btn"
                onclick={() => officialDialog.close()}>Cancel</button
            >
            <button
                type="button"
                class="v2-btn v2-btn-primary"
                disabled={saving || role === official.role}
                onclick={() => saveRole(official)}>Save</button
            >
        </div>
    {/if}
</dialog>
