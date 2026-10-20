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

<div class="a-frame">
    <PoolBand {headline} />

    <div class="a-side">
        <div class="a-top">
            <!-- phones only: the band carries the brand on desktop -->
            <a href={page.props.manage?.site.home} class="a-brand">
                <BrandMark size={28} />
                Scoring.Events
            </a>
            <ThemeToggle />
        </div>

        <main class="a-main">
            {#key page.component}
                <div class="a-content">
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

        <footer class="a-footer">
            <nav aria-label="Legal">
                <span>&copy; {new Date().getFullYear()} {page.props.legal.tradingAs}</span>
                <a href={page.props.legal.terms}>Terms</a>
                <a href={page.props.legal.privacy}>Privacy</a>
                <a href={page.props.legal.cookies}>Cookies</a>
                <a href="mailto:{page.props.legal.contactEmail}">Contact</a>
            </nav>
        </footer>
    </div>
</div>

<style>
    .a-frame {
        display: grid;
        min-height: 100dvh;
    }

    .a-side {
        display: flex;
        flex-direction: column;
        min-width: 0;
        padding: 0 1rem;
    }

    .a-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 0;
    }

    .a-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 800;
    }

    /* centred in the space between the toggle and the footer */
    .a-main {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 0 2.5rem;
    }

    /* a CSS animation, not in:fly: Svelte skips intros on the first mount. {#key} replays it per page */
    .a-content {
        width: 100%;
        max-width: 24rem;
        animation: a-rise 300ms ease-out both;
    }

    @keyframes a-rise {
        from {
            opacity: 0;
            transform: translateY(0.5rem);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .a-content {
            animation: none;
        }
    }

    /* The pages' shared parts: title, lead, and the form below them */
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

    .a-footer nav {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.25rem 1rem;
        padding: 1.25rem 0;
        font-size: 0.8125rem;
        color: var(--v2-muted);
    }

    .a-footer a:hover {
        color: var(--v2-ink);
    }

    @media (min-width: 64rem) {
        .a-frame {
            grid-template-columns: minmax(0, 5fr) minmax(0, 4fr);
        }

        .a-side {
            padding: 0 2.5rem;
        }

        .a-top {
            justify-content: flex-end;
        }

        .a-brand {
            display: none;
        }
    }
</style>
