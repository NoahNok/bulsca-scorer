<script module lang="ts">
    export const layout = {
        title: "Sign in to your account",
        description: "Enter your email and password below to sign in",
    };
</script>

<script lang="ts">
    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import Input from "@/components/input.svelte";
    import type { Competition } from "@/types/base";
    import { Form, Link, page } from "@inertiajs/svelte";
    import {
        home,
        joinCompetition,
    } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import {
        ArrowRight,
        Clipboard,
        FingerprintPattern,
        Home,
        Plus,
        PlusCircle,
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

<section class="flex flex-col absolute top-0 left-0 w-full p-6 z-10">
    <div class="flex w-full justify-between items-center">
        <div class="">
            <h1 class="  -mb-3 normal-case! text-black! text-base!">Digital</h1>
            <h1 class=" indent-6 normal-case! text-se text-xl!">Judge</h1>
        </div>

        <Clipboard
            class="bg-se/20 rounded-full text-se p-2 shadow-md "
            size={40}
        />
    </div>
</section>

<div class="h-16"></div>

<section class="flex flex-col h-full">
    <p class="font-archivo -mb-2">Welcome back,</p>
    <h2 class="">{user.name}</h2>
    <br />

    {#if $appState.activeCompetition}
        <h4 class=" capitalize! mb-3">
            <!-- svelte-ignore a11y_click_events_have_key_events -->
            <!-- svelte-ignore a11y_no_static_element_interactions -->
            Re-join Competition
            <span
                class="text-gray-500 lowercase! underline font-normal! hover:text-gray-800 hover:font-semibold cursor-pointer"
                onclick={() => ($appState.activeCompetition = undefined)}
                >or join another</span
            >
        </h4>
        <Link href={home($appState.activeCompetition)}>
            <Button
                variant="primary"
                class="w-full"
                label={$appState.activeCompetition.name}
                icon={ArrowRight}
            />
        </Link>
    {:else}
        <h4 class=" capitalize! mb-3">Join Competition</h4>
        <Form action={joinCompetition()} onError={(e) => toastError(e.pin)}>
            {#snippet children({ errors, processing })}
                <div class="flex space-x-3">
                    <Input
                        label="Competition Code"
                        placeholder="Enter competition PIN"
                        class="w-full"
                        name="pin"
                        type="number"
                    />

                    <Button variant="primary" class="" icon={ArrowRight} />
                </div>
                {#if errors.pin}
                    <small
                        transition:slide={{ duration: 100, y: 6 }}
                        class="text-red-500 self-end! -mt-2!"
                        >{errors.pin}</small
                    >
                {/if}
            {/snippet}
        </Form>
    {/if}

    <br />

    <h4 class=" capitalize!">Previously</h4>
    <div>
        {#each competitions as competition}
            <Link href={home(competition)} class="flex space-x-3 group">
                <div
                    class="border rounded-lg shadow-md p-4 group-hover:border-se focus:ring-1 focus:outline-none transition-all w-full"
                >
                    <div>
                        <p class="font-semibold uppercase text-xs!">
                            {competition.when}
                        </p>
                        <h3 class="normal-case! -mb-1">{competition.name}</h3>
                    </div>
                </div>

                <Button variant="secondary" class="" icon={ArrowRight} />
            </Link>
        {/each}
    </div>
</section>
