<script module lang="ts">
    import { Clipboard } from "@lucide/svelte";

    export const layout = {
        title: "Sign in to your account",
        description: "Enter your email and password below to sign in",
        header: { icon: Clipboard },
    };
</script>

<script lang="ts">
    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import EmptyState from "@/components/EmptyState.svelte";
    import Input from "@/components/input.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";
    import type { Competition } from "@/types/base";
    import { Form, Link, page } from "@inertiajs/svelte";
    import {
        home,
        joinCompetition,
    } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import {
        ArrowRight,
        CalendarDays,
        ChevronRight,
        History,
        LogIn,
    } from "@lucide/svelte";
    import { slide } from "svelte/transition";
    import { toastError } from "@/lib/toast.svelte";
    import { appState } from "@/lib/stores/appState";

    const user = $derived(page.props.auth.user);

    let {
        competitions,
    }: {
        competitions: Competition[];
    } = $props();
</script>

<AppHead title="Dashboard" />

<section class="flex flex-col">
    <p class="font-archivo -mb-2">Welcome back,</p>
    <h2>{user.name}</h2>

    {#if $appState.activeCompetition}
        <SectionLabel class="mt-4 mb-2">Continue judging</SectionLabel>
        <Link
            href={home($appState.activeCompetition)}
            class="group flex items-center gap-3 rounded-xl border border-se/40 bg-se/5 p-3 shadow-sm transition-all hover:border-se hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
        >
            <span
                class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-se text-white"
            >
                <LogIn size={18} />
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-xs text-gray-500">Rejoin</p>
                <p class="truncate font-semibold text-gray-900">
                    {$appState.activeCompetition.name}
                </p>
            </div>
            <ChevronRight
                size={18}
                class="shrink-0 text-se transition-transform group-hover:translate-x-0.5"
            />
        </Link>
        <button
            type="button"
            class="mt-2 cursor-pointer self-end text-sm text-gray-500 underline hover:text-gray-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
            onclick={() => ($appState.activeCompetition = undefined)}
        >
            Join a different competition
        </button>
    {:else}
        <SectionLabel class="mt-4 mb-2">Join a competition</SectionLabel>
        <Form action={joinCompetition()} onError={(e) => toastError(e.pin)}>
            {#snippet children({ errors, processing })}
                <div class="flex gap-2">
                    <Input
                        variant="soft"
                        placeholder="Competition PIN"
                        name="pin"
                        type="number"
                        inputmode="numeric"
                        autocomplete="off"
                        aria-label="Competition PIN"
                        class="min-w-0 flex-1"
                    />
                    <Button
                        variant="primary"
                        class="ml-0 shrink-0"
                        icon={ArrowRight}
                        loading={processing}
                        aria-label="Join competition"
                    />
                </div>
                {#if errors.pin}
                    <p
                        transition:slide={{ duration: 100 }}
                        class="mt-1 text-xs text-red-600"
                    >
                        {errors.pin}
                    </p>
                {/if}
            {/snippet}
        </Form>
    {/if}

    <SectionLabel class="mt-6 mb-2">Your competitions</SectionLabel>
    {#if competitions.length === 0}
        <EmptyState
            icon={History}
            title="No competitions yet"
            description="Competitions you've judged at will show up here."
        />
    {:else}
        <div class="flex flex-col gap-2">
            {#each competitions as competition (competition.id)}
                <Link
                    href={home(competition)}
                    class="group flex items-center gap-3 rounded-xl border bg-white p-3 shadow-sm transition-all hover:border-se hover:shadow-md focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                >
                    <span
                        class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-600 transition-colors group-hover:bg-se/20 group-hover:text-teal-700"
                    >
                        <CalendarDays size={18} />
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-semibold text-gray-900">
                            {competition.name}
                        </p>
                        <p class="truncate text-sm text-gray-500">
                            {[competition.when, competition.where]
                                .filter(Boolean)
                                .join(" · ") || "–"}
                        </p>
                    </div>
                    <ChevronRight
                        size={18}
                        class="shrink-0 text-gray-300 transition-all group-hover:translate-x-0.5 group-hover:text-se"
                    />
                </Link>
            {/each}
        </div>
    {/if}
</section>
