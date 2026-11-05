<!--
    A competition's or organisation's Accounts: who can manage it and what they can open, then the invites
    not accepted yet, each with Cancel invite. A row opens a dialog to change their access or remove them
    (not the owner, and not you); Invite someone opens one to invite by email, suggesting existing
    accounts as you type. The access checkboxes come in groups, each with an optional heading.
-->
<script lang="ts" module>
    import type { RouteDefinition } from "@/wayfinder";

    export type AccessGroup = { label: string | null; types: Record<string, string> };

    /** Where its forms and buttons go */
    export type AccountsRoutes = {
        invite: RouteDefinition<"post">;
        update: (account: number) => RouteDefinition<"post">;
        remove: (account: number) => string;
        cancelInvite: (invite: string) => string;
    };
</script>

<script lang="ts">
    import { confirm } from "@/lib/confirm";
    import Field from "@/manage/components/Field.svelte";
    import type { Account, PendingInvite } from "@/manage/types";
    import { closeOnBackdrop } from "@/manage/ui";
    import { search } from "@/routes/accounts";
    import { Form, router } from "@inertiajs/svelte";
    import { tick } from "svelte";

    let {
        accounts,
        invites,
        accessGroups,
        of,
        adminHint,
        inviteDefaults = [],
        routes,
        headingId,
    }: {
        accounts: Account[];
        invites: PendingInvite[];
        accessGroups: AccessGroup[];
        /** What they manage, as in "They own …": "this competition" */
        of: string;
        /** Under the access checkboxes: what Admin gives */
        adminHint: string;
        /** Ticked when inviting */
        inviteDefaults?: string[];
        routes: AccountsRoutes;
        /** For the section's aria-labelledby */
        headingId: string;
    } = $props();

    const accessNames = $derived(Object.assign({}, ...accessGroups.map((group) => group.types)) as Record<string, string>);

    function accessSummary(account: Account) {
        if (account.owner) return "Owner";
        if (account.access.includes("admin")) return "Everything";
        return account.access.map((key) => accessNames[key] ?? key).join(", ") || "None";
    }

    let editing = $state<Account | null>(null);
    let editDialog: HTMLDialogElement;
    let inviteDialog: HTMLDialogElement;
    let removeError = $state<string | null>(null);

    async function edit(account: Account) {
        editing = account;
        removeError = null;
        await tick();
        editDialog.showModal();
    }

    async function remove(account: Account) {
        const ok = await confirm({
            title: `Remove ${account.name}?`,
            description: `They won't be able to manage ${of} any more.`,
            confirmLabel: "Remove",
            confirmVariant: "danger",
        });
        if (!ok) return;

        router.delete(routes.remove(account.id), {
            preserveScroll: true,
            onSuccess: () => editDialog.close(),
            onError: (errors) => (removeError = errors.account ?? "They couldn't be removed."),
        });
    }

    async function cancelInvite(invite: PendingInvite) {
        const ok = await confirm({
            title: `Cancel the invite to ${invite.email}?`,
            description: "The link in their email will stop working. You can invite them again later.",
            confirmLabel: "Cancel invite",
            cancelLabel: "Keep it",
            confirmVariant: "danger",
        });
        if (ok) router.delete(routes.cancelInvite(invite.id), { preserveScroll: true });
    }

    // Existing accounts as you type an email, as the browser's own suggestions
    let suggestions = $state<{ name: string; email: string }[]>([]);
    let searchTimer: ReturnType<typeof setTimeout>;

    function suggest(event: Event) {
        const email = (event.currentTarget as HTMLInputElement).value.trim();
        clearTimeout(searchTimer);
        if (email.length < 3) {
            suggestions = [];
            return;
        }
        searchTimer = setTimeout(async () => {
            const response = await fetch(search(email).url, { headers: { Accept: "application/json" } });
            suggestions = response.ok ? await response.json() : [];
        }, 200);
    }

    const fieldset = "mb-5 flex flex-col gap-[0.6rem] *:mt-0";
    const accessGrid = "grid grid-cols-2 gap-x-4 gap-y-[0.6rem] *:mt-0";
</script>

{#snippet accessChecks(checked: string[])}
    {#each accessGroups as group, i (i)}
        {#if group.label}<h3 class="text-xs font-bold tracking-wide text-v2-muted uppercase">{group.label}</h3>{/if}
        <div class={accessGrid}>
            {#each Object.entries(group.types) as [key, label] (key)}
                <label class="v2-check">
                    <input type="checkbox" name="access[]" value={key} checked={checked.includes(key)} />
                    {label}
                </label>
            {/each}
        </div>
    {/each}
    <span class="v2-hint">{adminHint}</span>
{/snippet}

<div class="flex items-center justify-between gap-4">
    <h2 id={headingId} class="text-lg font-extrabold uppercase">Accounts</h2>
    <button type="button" class="v2-btn v2-btn-sm" onclick={() => inviteDialog.showModal()}>Invite someone</button>
</div>
<p class="mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted">People who can manage {of}, and what they can open.</p>

<table class="w-full border-t border-v2-border">
    <thead class="sr-only">
        <tr>
            <th>Name</th>
            <th>Access</th>
        </tr>
    </thead>
    <tbody>
        {#each accounts as account (account.id)}
            <tr class="v2-row *:border-b *:border-v2-border *:px-2 *:py-3 *:align-middle">
                <td>
                    <button type="button" class="v2-row-link flex cursor-pointer flex-col items-start text-left focus-visible:outline-none" onclick={() => edit(account)}>
                        <span class="font-bold">{account.name}{account.you ? " (you)" : ""}</span>
                        <span class="text-sm text-v2-muted">{account.email}</span>
                    </button>
                </td>
                <td class="text-right text-sm text-v2-muted">{accessSummary(account)}</td>
            </tr>
        {/each}
        {#each invites as invite (invite.id)}
            <tr class="*:border-b *:border-v2-border *:px-2 *:py-3 *:align-middle">
                <td>
                    <span class="flex flex-col items-start">
                        <span class="font-bold">{invite.email}</span>
                        <span class="text-sm text-v2-muted">Invited, not joined yet</span>
                    </span>
                </td>
                <td class="text-right">
                    <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm text-v2-danger-text" onclick={() => cancelInvite(invite)}>
                        Cancel invite<span class="sr-only"> to {invite.email}</span>
                    </button>
                </td>
            </tr>
        {/each}
    </tbody>
</table>

<dialog bind:this={editDialog} class="v2-modal" aria-labelledby="{headingId}-edit-title" onclick={closeOnBackdrop}>
    {#if editing}
        {@const account = editing}
        <h2 id="{headingId}-edit-title">{account.name}</h2>
        <p>{account.email}</p>

        {#if account.owner}
            <p class="mt-5">They own {of}, so they can do everything.</p>
            <div class="v2-modal-actions">
                <button type="button" class="v2-btn" onclick={() => editDialog.close()}>Close</button>
            </div>
        {:else}
            {#key account.id}
                <Form action={routes.update(account.id)} options={{ preserveScroll: true }} onSuccess={() => editDialog.close()}>
                    {#snippet children({ errors, processing })}
                        <fieldset class="mt-5 {fieldset}">
                            <legend class="mb-[0.6rem] text-sm font-semibold">What they can open</legend>
                            {@render accessChecks(account.access)}
                            {#if errors.access}<span class="v2-error">{errors.access}</span>{/if}
                            {#if removeError}<span class="v2-error">{removeError}</span>{/if}
                        </fieldset>
                        <div class="v2-modal-actions">
                            {#if !account.you}
                                <button type="button" class="v2-btn v2-btn-ghost mr-auto text-v2-danger-text" onclick={() => remove(account)}>Remove</button>
                            {/if}
                            <button type="button" class="v2-btn" onclick={() => editDialog.close()}>Cancel</button>
                            <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Save access</button>
                        </div>
                    {/snippet}
                </Form>
            {/key}
        {/if}
    {/if}
</dialog>

<dialog bind:this={inviteDialog} class="v2-modal" aria-labelledby="{headingId}-invite-title" onclick={closeOnBackdrop}>
    <h2 id="{headingId}-invite-title">Invite someone</h2>
    <p>We'll email them a link. If they don't have an account yet, they'll make one.</p>

    <Form action={routes.invite} options={{ preserveScroll: true }} resetOnSuccess onSuccess={() => inviteDialog.close()}>
        {#snippet children({ errors, processing })}
            <div class="mt-5">
                <Field
                    id="{headingId}-invite-email"
                    name="email"
                    type="email"
                    label="Email"
                    required
                    autocomplete="off"
                    list="{headingId}-invite-suggestions"
                    error={errors.email}
                    oninput={suggest}
                />
                <datalist id="{headingId}-invite-suggestions">
                    {#each suggestions as suggestion (suggestion.email)}
                        <option value={suggestion.email}>{suggestion.name}</option>
                    {/each}
                </datalist>
            </div>
            <fieldset class="mt-5 {fieldset}">
                <legend class="mb-[0.6rem] text-sm font-semibold">What they can open</legend>
                {@render accessChecks(inviteDefaults)}
                {#if errors.access}<span class="v2-error">Choose what they can open.</span>{/if}
            </fieldset>
            <div class="v2-modal-actions">
                <button type="button" class="v2-btn" onclick={() => inviteDialog.close()}>Cancel</button>
                <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Send invite</button>
            </div>
        {/snippet}
    </Form>
</dialog>
