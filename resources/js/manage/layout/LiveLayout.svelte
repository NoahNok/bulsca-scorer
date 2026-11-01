<!--
    Shell for the live pages (pages/Live/*, on live., public and signed out) but the full-screen display: the
    v2 header with the brand (to Explore), the live status and clock, and the theme toggle; the
    competition's name, date, venue and logo with the live tabs; and the footer. It reads the page's
    `competition` prop (LiveController::header). The Unavailable page has none, so gets just the shell.
-->
<script lang="ts">
    import BrandMark from "@/manage/components/BrandMark.svelte";
    import PageTabs from "@/manage/components/PageTabs.svelte";
    import ThemeToggle from "@/manage/components/ThemeToggle.svelte";
    import LiveStatus from "@/manage/live/LiveStatus.svelte";
    import type { LiveHeader, PageTab } from "@/manage/types";
    import { page } from "@inertiajs/svelte";
    import type { Snippet } from "svelte";

    let { children }: { children?: Snippet } = $props();

    const comp = $derived(page.props.competition as LiveHeader | undefined);

    const tabs: PageTab[] = $derived(
        comp
            ? [
                  { label: "Live", url: comp.links.live, current: page.component == "Live/Index", svelte: true },
                  { label: "DQs & Penalties", url: comp.links.dqs, current: page.component.startsWith("Live/") && page.component.includes("Dqs"), svelte: true },
                  { label: "Full screen", url: comp.links.display, current: false, svelte: false, newTab: true },
                  { label: "Heats & Draws", url: comp.links.heatsDraws, current: false, svelte: false },
              ]
            : [],
    );

    function longDate(ymd: string): string {
        return new Date(`${ymd}T00:00`).toLocaleDateString("en-GB", { day: "numeric", month: "short", year: "numeric" });
    }
</script>

<div class="flex min-h-dvh flex-col">
    <header class="v2-header">
        <a href={page.props.live?.site.explore} class="v2-brand">
            <BrandMark size={32} live />
            Scoring.Events
        </a>
        <div class="flex items-center gap-4">
            {#if comp}
                <div class="max-sm:hidden"><LiveStatus url={comp.dataUrl} /></div>
            {/if}
            <ThemeToggle />
        </div>
    </header>

    <main class="v2-container flex-1 pt-[0.5625rem] pb-6 sm:pt-3">
        {#if comp}
            <!-- On phones the status sits under the header, where there's room for it -->
            <div class="mb-4 sm:hidden"><LiveStatus url={comp.dataUrl} /></div>

            <header class="flex items-center justify-between gap-6">
                <div class="min-w-0">
                    <h1 class="text-[clamp(1.75rem,3.5vw,2.5rem)] leading-[1.1] font-extrabold wrap-anywhere uppercase">{comp.name}</h1>
                    <p class="mt-1 text-[0.9375rem] text-v2-muted">
                        <span class="font-anton tracking-wide">{longDate(comp.when)}</span> · {comp.where}
                    </p>
                </div>
                {#if comp.logo}
                    <img src={comp.logo} alt="" class="size-14 shrink-0 rounded-full border border-v2-border object-contain max-sm:size-11" />
                {/if}
            </header>

            {#key comp.id}
                <PageTabs {tabs} label="Live" />
            {/key}

            <div class="mt-8">
                {@render children?.()}
            </div>
        {:else}
            {@render children?.()}
        {/if}
    </main>

    <footer class="v2-footer">
        <nav aria-label="Legal">
            <span>&copy; {new Date().getFullYear()} {page.props.legal.tradingAs}</span>
            <a href={page.props.legal.terms}>Terms</a>
            <a href={page.props.legal.privacy}>Privacy</a>
            <a href={page.props.legal.cookies}>Cookies</a>
            <a href="mailto:{page.props.legal.contactEmail}">Contact</a>
        </nav>
    </footer>
</div>
