<!--
    Shell for every Management and Admin page: the v2 header and footer (resources/css/v2.css),
    the same chrome as landing/v2/layout.blade.php. Pages render inside a v2-container.
-->
<script lang="ts">
    import BrandMark from "@/manage/components/BrandMark.svelte";
    import ConfirmDialog from "@/manage/components/ConfirmDialog.svelte";
    import ThemeToggle from "@/manage/components/ThemeToggle.svelte";
    import Toasts from "@/manage/components/Toasts.svelte";
    import { toastError, toastSuccess } from "@/lib/toast.svelte";
    import { guardUnsaved } from "@/manage/unsaved";
    import { account, logout } from "@/routes";
    import manage from "@/routes/manage";
    import { index as adminHome } from "@/routes/manage/admin/competitions";
    import { Link, page, router } from "@inertiajs/svelte";
    import { onMount, type Snippet } from "svelte";

    let { children }: { children?: Snippet } = $props();

    // Forms with UnsavedChanges in them ask before you leave
    onMount(guardUnsaved);

    // A save's "Settings saved", or an error a redirect brought (like an invite that's no longer valid): on the
    // first page, then after every visit (each brings its own flash)
    function toastFlash(flash: { success?: string | null; error?: string | null } | undefined) {
        if (flash?.success) toastSuccess(flash.success);
        if (flash?.error) toastError(flash.error);
    }

    onMount(() => {
        toastFlash(page.props.manage?.flash);

        return router.on("success", (event) => toastFlash(event.detail.page.props.manage?.flash));
    });
</script>

<div class="flex min-h-dvh flex-col">
    <header class="v2-header">
        <Link href={manage.home().url} class="v2-brand">
            <BrandMark size={32} />
            Scoring.Events
        </Link>
        <!-- Explore (the public site) and Sign out stay full page loads -->
        <nav class="v2-nav" aria-label="Main">
            <a href={page.props.manage?.site.explore} class="v2-nav-wide">Explore</a>
            {#if page.props.manage?.isAdmin}
                <Link href={adminHome().url}>Admin</Link>
            {/if}
            <Link href={account().url}>Account</Link>
            <a href={logout().url}>Sign out</a>
            <ThemeToggle />
        </nav>
    </header>

    <!-- Room under the header before each page's title -->
    <main class="v2-container flex-1 pt-[0.5625rem] pb-6 sm:pt-3">
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

    <Toasts />
    <ConfirmDialog />
</div>
