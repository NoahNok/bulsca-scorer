<script lang="ts">
    import { account } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/AccountController";
    import { logout } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/LoginController";
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { confirm } from "@/lib/confirm";
    import { appState } from "@/lib/stores/appState";
    import { submissions } from "@/routes/judge/competition/violation";
    import type { Competition } from "@/types/base";
    import { Link, page, router } from "@inertiajs/svelte";
    import { Flag, House, LogOut, Shield, UserRound } from "@lucide/svelte";

    // without a competition (the dashboard and account pages) the bar offers
    // "Account" and "Log out"
    let { competition }: { competition?: Competition } = $props();

    const isHeadRef = $derived(!!competition && page.props.judge.isHeadRef);

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
        class="grid grid-cols-2 sm:mx-auto sm:max-w-[70%] xl:max-w-[50%] 2xl:max-w-[40%]"
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
