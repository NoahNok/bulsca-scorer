<script lang="ts">
    import { account } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/AccountController";
    import { logout } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/LoginController";
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { alerts } from "@/lib/alerts.svelte";
    import { announcements } from "@/lib/announcements.svelte";
    import { confirm } from "@/lib/confirm";
    import { echoEnabled } from "@/lib/echo";
    import { presence } from "@/lib/presence.svelte";
    import { appState } from "@/lib/stores/appState";
    import { submissions } from "@/routes/judge/competition/violation";
    import type { Competition } from "@/types/base";
    import { Link, page, router } from "@inertiajs/svelte";
    import { Bell, Flag, House, LogOut, Shield, UserRound, Users } from "@lucide/svelte";

    // without a competition (the dashboard and account pages) the bar offers
    // "Account" and "Log out". With one, "Alerts" opens the alerts history and
    // "Online" the officials sheet when Reverb is configured
    let { competition }: { competition?: Competition } = $props();

    const isHeadRef = $derived(!!competition && page.props.judge.isHeadRef);
    const showOnline = $derived(!!competition && echoEnabled);
    // DQ/Penalty alerts and Announcements share the tab and its sheet
    const unread = $derived(alerts.unread + announcements.unread);

    async function logOut() {
        const ok = await confirm({
            title: "Log out",
            description: "Log out of Digital Judge on this device?",
            confirmLabel: "Log out",
        });
        if (!ok) return;

        // the device may be handed to another judge, so forget the rejoin
        $appState.activeCompetition = undefined;
        router.post(logout());
    }

    const tabs = $derived(!competition ? [
        {
            label: "Account",
            icon: UserRound,
            href: account().url,
            active: page.component === "Judge/Account",
        },
    ] : [
        {
            label: "Home",
            icon: House,
            href: home(competition).url,
            active: page.component === "Judge/Competition/Home",
        },
        {
            label: "DQ/Penalty",
            icon: Flag,
            href: submissions(competition).url,
            active: page.component.startsWith("Judge/Competition/Violation/"),
        },
    ]);

    const tabClass =
        "group flex min-h-14 w-full cursor-pointer flex-col items-center justify-center gap-0.5 py-2 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-se focus-visible:ring-inset";
</script>

<nav
    aria-label={competition ? "Competition" : "Account"}
    class="fixed inset-x-0 bottom-0 z-40 border-t bg-white pb-[env(safe-area-inset-bottom)] {isHeadRef
        ? 'border-se/40'
        : ''}"
>
    {#if isHeadRef}
        <p
            class="font-archivo flex items-center justify-center gap-1.5 bg-se/10 py-1 text-xs font-semibold tracking-wide text-teal-800 uppercase"
        >
            <Shield size={14} /> Referee mode
        </p>
    {/if}
    <div
        class="grid {showOnline
            ? 'grid-cols-4'
            : 'grid-cols-2'} sm:mx-auto sm:max-w-[70%] xl:max-w-[50%] 2xl:max-w-[40%]"
    >
        {#each tabs as { label, icon: Icon, href, active } (label)}
            <Link
                {href}
                aria-current={active ? "page" : undefined}
                class="{tabClass} {active
                    ? 'text-teal-700'
                    : 'text-gray-500 hover:text-gray-900'}"
            >
                <span
                    class="flex h-8 w-14 items-center justify-center rounded-full transition-colors {active
                        ? 'bg-se/20'
                        : 'group-hover:bg-gray-100'}"
                >
                    <Icon size={22} />
                </span>
                <span class="text-xs font-semibold">{label}</span>
            </Link>
        {/each}

        {#if showOnline}
            <button
                type="button"
                aria-haspopup="dialog"
                aria-label={unread
                    ? `Alerts: ${unread} new`
                    : "Alerts"}
                class="{tabClass} {alerts.sheetOpen
                    ? 'text-teal-700'
                    : 'text-gray-500 hover:text-gray-900'}"
                onclick={() => (alerts.sheetOpen = true)}
            >
                <span
                    class="relative flex h-8 w-14 items-center justify-center rounded-full transition-colors {alerts.sheetOpen
                        ? 'bg-se/20'
                        : 'group-hover:bg-gray-100'}"
                >
                    <Bell size={22} />
                    {#if unread}
                        <span
                            class="absolute top-0 right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-se px-1 text-[10px] leading-none font-bold text-white"
                        >
                            {unread > 9 ? "9+" : unread}
                        </span>
                    {/if}
                </span>
                <span class="text-xs font-semibold">Alerts</span>
            </button>

            <button
                type="button"
                aria-haspopup="dialog"
                aria-label="Officials online: {presence.count}"
                class="{tabClass} {presence.sheetOpen
                    ? 'text-teal-700'
                    : 'text-gray-500 hover:text-gray-900'}"
                onclick={() => (presence.sheetOpen = true)}
            >
                <span
                    class="relative flex h-8 w-14 items-center justify-center rounded-full transition-colors {presence.sheetOpen
                        ? 'bg-se/20'
                        : 'group-hover:bg-gray-100'}"
                >
                    <Users size={22} />
                    {#if presence.connected}
                        <span
                            class="absolute top-0 right-1 flex h-4 min-w-4 items-center justify-center rounded-full bg-se px-1 text-[10px] leading-none font-bold text-white"
                        >
                            {presence.count}
                        </span>
                    {/if}
                </span>
                <span class="flex items-center gap-1 text-xs font-semibold">
                    {#if presence.connected}
                        <span class="relative flex size-1.5">
                            <span
                                class="absolute inline-flex size-full animate-ping rounded-full bg-se opacity-60"
                            ></span>
                            <span
                                class="relative inline-flex size-1.5 rounded-full bg-se"
                            ></span>
                        </span>
                    {/if}
                    Online
                </span>
            </button>
        {/if}

        {#if !competition}
            <button
                type="button"
                class="{tabClass} text-gray-500 hover:text-gray-900"
                onclick={logOut}
            >
                <span
                    class="flex h-8 w-14 items-center justify-center rounded-full transition-colors group-hover:bg-gray-100"
                >
                    <LogOut size={22} />
                </span>
                <span class="text-xs font-semibold">Log out</span>
            </button>
        {/if}
    </div>
</nav>
