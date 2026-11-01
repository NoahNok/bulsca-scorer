<!--
    A competition's Admin page (manage.admin.competitions.show), in AdminLayout: only what Admins alone
    can change (its organisation and the anytime pin), with links
    to the competition in Management and its public page for everything else. Moving it to another
    organisation asks first, since that changes who can manage it.
-->
<script lang="ts">
    import { confirm } from "@/lib/confirm";
    import { shortDate } from "@/lib/dates";
    import PageHeading from "@/manage/components/PageHeading.svelte";
    import Switch from "@/manage/components/Switch.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import { index, update } from "@/routes/manage/admin/competitions";
    import type { PendingVisit } from "@inertiajs/core";
    import { Form, Link } from "@inertiajs/svelte";

    let {
        competition,
        organisations,
        manageUrl,
        publicUrl,
    }: {
        competition: {
            id: number;
            name: string;
            /** Y-m-d */
            when: string;
            organisation: number | null;
            anytimepin: boolean;
        };
        organisations: { id: number; name: string }[];
        manageUrl: string;
        publicUrl: string;
    } = $props();

    let form: { submit: () => void } | undefined = $state();
    // Set once the move is confirmed, so the save that follows goes ahead
    let moveConfirmed = false;

    const orgName = (id: number | null) => (id === null ? null : (organisations.find((org) => org.id === id)?.name ?? null));

    function confirmMove(visit: PendingVisit) {
        const picked = String((visit.data as Record<string, unknown>).organisation ?? "");
        const to = picked === "" ? null : Number(picked);
        if (to === competition.organisation || moveConfirmed) {
            moveConfirmed = false;
            return;
        }

        const from = orgName(competition.organisation);
        const into = orgName(to);
        confirm({
            title: `Move ${competition.name}?`,
            description: [
                from ? `${from}'s Organisers lose access to it.` : null,
                into ? `${into}'s Organisers can manage it.` : "It becomes a Personal competition.",
                "Organisers added to the competition itself keep their access.",
            ]
                .filter(Boolean)
                .join(" "),
            confirmLabel: "Move and save",
        }).then((ok) => {
            if (!ok) return;
            moveConfirmed = true;
            form?.submit();
        });
        return false;
    }
</script>

<svelte:head>
    <title>{competition.name} - Admin - Scoring.Events</title>
</svelte:head>

<PageHeading back={{ href: index().url, label: "Competitions" }} title={competition.name} lead={shortDate(competition.when)}>
    {#snippet actions()}
        <div class="flex flex-wrap gap-2">
            <a href={publicUrl} class="v2-btn v2-btn-sm">Public page</a>
            <Link href={manageUrl} class="v2-btn v2-btn-accent v2-btn-sm">Open in Management</Link>
        </div>
    {/snippet}
</PageHeading>

<section aria-labelledby="acp-heading" class="max-w-lg">
    <h3 id="acp-heading" class="text-lg font-extrabold uppercase">Admin settings</h3>
    <p class="mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted">
        Only Admins can change these. Its name, date, venue, lanes and deleting it are in its Management Settings.
    </p>

    <Form bind:this={form} action={update(competition.id).url} method="post" options={{ preserveScroll: true }} setDefaultsOnSuccess onBefore={confirmMove}>
        {#snippet children({ errors, processing, isDirty })}
            <div class="v2-field">
                <label for="acp-organisation">Organisation</label>
                <select id="acp-organisation" name="organisation" class={["v2-input w-full", errors.organisation && "is-invalid"]}>
                    <option value="" selected={competition.organisation === null}>Personal (no organisation)</option>
                    {#each organisations as org (org.id)}
                        <option value={org.id} selected={competition.organisation === org.id}>{org.name}</option>
                    {/each}
                </select>
                {#if errors.organisation}<p class="v2-error">{errors.organisation}</p>{/if}
            </div>

            <div class="mt-5">
                <Switch name="anytimepin" label="Anytime pin" checked={competition.anytimepin} hint="Officials can join the Legacy Digital Judge with its pin at any time, not only on the day." />
            </div>

            <UnsavedChanges dirty={isDirty} />
            <div class="mt-6 flex items-center justify-end gap-4">
                {#if isDirty}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
                <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Save</button>
            </div>
        {/snippet}
    </Form>
</section>
