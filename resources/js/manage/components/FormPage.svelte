<!--
    A page that's one form (new competition, new organisation): a "Home" link back, the title and a lead,
    then the form, in a narrow column centred in the container. End the form with a .p-actions row: the
    SubmitButton, and a muted Cancel link under it.
-->
<script lang="ts">
    import { home } from "@/routes/manage";
    import { Link } from "@inertiajs/svelte";
    import { ArrowLeft } from "@lucide/svelte";
    import type { Snippet } from "svelte";

    let { title, lead, children }: { title: string; lead: string; children: Snippet } = $props();
</script>

<div class="p-page mx-auto max-w-lg">
    <Link href={home()} class="v2-link-muted inline-flex items-center gap-1 text-sm"><ArrowLeft size={16} aria-hidden="true" /> Home</Link>
    <h1 class="mt-4 text-[1.75rem] leading-[1.1] font-extrabold wrap-anywhere uppercase">{title}</h1>
    <p class="mt-2 leading-[1.6] text-v2-muted">{lead}</p>

    {@render children()}
</div>

<style>
    /* The form and its .p-actions are the page's, so they're styled from here. The form's gap spaces the fields */
    .p-page :global(form) {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        margin-top: 1.75rem;
    }

    .p-page :global(form .v2-field + .v2-field) {
        margin-top: 0;
    }

    .p-page :global(.p-actions) {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        margin-top: 0.5rem;
    }
</style>
