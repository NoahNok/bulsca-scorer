<!--
    Frame for the signed-out pages (pages/Manage/Auth/): the pool band beside the form on desktop, in place of
    ManageLayout's header and footer. It stays mounted between these pages, so the band only draws in once
    and its line crossfades. See "The Management app" in docs/v2-ui-style-guide.md.
-->
<script lang="ts">
    import Alert from "@/manage/components/Alert.svelte";
    import BrandMark from "@/manage/components/BrandMark.svelte";
    import PoolBand from "@/manage/components/PoolBand.svelte";
    import ThemeToggle from "@/manage/components/ThemeToggle.svelte";
    import { page } from "@inertiajs/svelte";
    import type { Snippet } from "svelte";

    let { children }: { children?: Snippet } = $props();

    // the band's line for each page
    const headlines: Record<string, string> = {
        "Manage/Auth/SignIn": "Run the day. We'll keep the score.",
        "Manage/Auth/Register": "Accounts come by invite.",
        "Manage/Auth/InviteNewAccount": "You're on the team.",
        "Manage/Auth/ForgotPassword": "Happens to the best of us.",
        "Manage/Auth/ResetPassword": "Fresh start.",
    };

    const headline = $derived(headlines[page.component] ?? headlines["Manage/Auth/SignIn"]);
    const flash = $derived(page.props.manage?.flash);
</script>

<div class="grid min-h-dvh lg:grid-cols-[minmax(0,5fr)_minmax(0,4fr)]">
    <PoolBand {headline} />

    <div class="flex min-w-0 flex-col px-4 lg:px-10">
        <div class="flex items-center justify-between py-4 lg:justify-end">
            <!-- phones only: the band carries the brand on desktop -->
            <a href={page.props.manage?.site.home} class="inline-flex items-center gap-2 font-extrabold lg:hidden">
                <BrandMark size={28} />
                Scoring.Events
            </a>
            <ThemeToggle />
        </div>

        <!-- centred in the space between the toggle and the footer -->
        <main class="flex flex-1 items-center justify-center pt-2 pb-10">
            {#key page.component}
                <!-- A CSS animation, not in:fly: Svelte skips intros on the first mount. {#key} replays it per page -->
                <div class="a-content w-full max-w-sm animate-rise motion-reduce:animate-none">
                    {#if flash?.status}
                        <Alert tone="success">{flash.status}</Alert>
                    {/if}
                    {#if flash?.error}
                        <Alert tone="danger">{flash.error}</Alert>
                    {/if}
                    {@render children?.()}
                </div>
            {/key}
        </main>

        <footer>
            <nav aria-label="Legal" class="flex flex-wrap justify-center gap-x-4 gap-y-1 py-5 text-[0.8125rem] text-v2-muted">
                <span>&copy; {new Date().getFullYear()} {page.props.legal.tradingAs}</span>
                <a href={page.props.legal.terms} class="hover:text-v2-ink">Terms</a>
                <a href={page.props.legal.privacy} class="hover:text-v2-ink">Privacy</a>
                <a href={page.props.legal.cookies} class="hover:text-v2-ink">Cookies</a>
                <a href="mailto:{page.props.legal.contactEmail}" class="hover:text-v2-ink">Contact</a>
            </nav>
        </footer>
    </div>
</div>

<style>
    /* The pages' shared parts, used by all five: title, lead, and the form below them */
    .a-content :global(.a-title) {
        font-weight: 800;
        font-size: 1.75rem;
        line-height: 1.1;
        text-transform: uppercase;
        overflow-wrap: anywhere;
    }

    .a-content :global(.a-lead) {
        margin-top: 0.5rem;
        color: var(--v2-muted);
        line-height: 1.6;
    }

    .a-content :global(.v2-alert) {
        margin-bottom: 1.25rem;
    }

    .a-content :global(.a-form) {
        display: flex;
        flex-direction: column;
        gap: 1.25rem;
        margin-top: 1.75rem;
    }

    /* the form's gap spaces the fields */
    .a-content :global(.a-form .v2-field + .v2-field) {
        margin-top: 0;
    }

    /* Leads somewhere else: a muted line under a rule */
    .a-content :global(.a-aside) {
        margin-top: 2rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--v2-border);
        font-size: 0.875rem;
        color: var(--v2-muted);
    }
</style>
