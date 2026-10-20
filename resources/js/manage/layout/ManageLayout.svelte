<!--
    Shell for every Management and Admin page: the v2 header and footer (resources/css/v2.css),
    the same chrome as landing/v2/layout.blade.php. Pages render inside a v2-container.
-->
<script lang="ts">
    import BrandMark from "@/manage/components/BrandMark.svelte";
    import ThemeToggle from "@/manage/components/ThemeToggle.svelte";
    import { dashboard, logout } from "@/routes";
    import admin from "@/routes/admin";
    import { page } from "@inertiajs/svelte";
    import type { Snippet } from "svelte";

    let { children }: { children?: Snippet } = $props();
</script>

<div class="flex min-h-dvh flex-col">
    <header class="v2-header">
        <!-- A plain link while the dashboard is still Blade: Inertia can't load it in place -->
        <a href={dashboard().url} class="v2-brand">
            <BrandMark size={32} />
            Scoring.Events
        </a>
        <nav class="v2-nav" aria-label="Main">
            <a href={page.props.manage?.site.explore} class="v2-nav-wide">Explore</a>
            {#if page.props.manage?.isAdmin}
                <a href={admin.index().url}>Admin</a>
            {/if}
            <a href={logout().url}>Sign out</a>
            <ThemeToggle />
        </nav>
    </header>

    <main class="v2-container flex-1 py-6">
        {@render children?.()}
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
