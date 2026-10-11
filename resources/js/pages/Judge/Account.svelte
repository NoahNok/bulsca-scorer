<script module lang="ts">
    import { index } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { UserRound } from "@lucide/svelte";

    export const layout = {
        header: { icon: UserRound, href: index() },
    };
</script>

<script lang="ts">
    import {
        updateAccount,
        updatePassword,
    } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/AccountController";
    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import Button from "@/components/Button.svelte";
    import Input from "@/components/input.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";
    import { Form } from "@inertiajs/svelte";
    import { Check, KeyRound } from "@lucide/svelte";
    import { slide } from "svelte/transition";

    let {
        name,
        email,
    }: {
        name: string;
        email: string;
    } = $props();
</script>

<AppHead title="Your account" />

{#snippet fieldError(error: string | undefined)}
    {#if error}
        <p transition:slide={{ duration: 100 }} class="mt-1 text-xs text-red-600">
            {error}
        </p>
    {/if}
{/snippet}

<section class="flex flex-col">
    <p class="font-archivo -mb-2">Officials</p>
    <h2>Your account</h2>

    <BackLink href={index()} label="Dashboard" class="mt-2 mb-4" />

    <dl class="divide-y rounded-xl border bg-white">
        <div class="flex items-center justify-between gap-3 p-3">
            <dt class="text-xs text-gray-500">Email</dt>
            <dd class="min-w-0 truncate text-sm font-semibold">{email}</dd>
        </div>
    </dl>

    <Form
        action={updateAccount()}
        options={{ preserveScroll: true }}
        class="grid grid-cols-2 gap-x-3 gap-y-3"
    >
        {#snippet children({ errors, processing })}
            <SectionLabel class="col-span-2 mt-6 -mb-1">Details</SectionLabel>

            <div class="col-span-2">
                <Input
                    variant="soft"
                    label="Name"
                    name="name"
                    value={name}
                    autocomplete="name"
                    required
                />
                {@render fieldError(errors.name)}
            </div>

            <Button
                type="submit"
                variant="primary"
                label="Save"
                icon={Check}
                loading={processing}
                class="col-span-2 w-full"
            />
        {/snippet}
    </Form>

    <Form
        action={updatePassword()}
        options={{ preserveScroll: true }}
        resetOnSuccess
        resetOnError
        class="grid grid-cols-2 gap-x-3 gap-y-3"
    >
        {#snippet children({ errors, processing })}
            <SectionLabel class="col-span-2 mt-6 -mb-1">
                Change password
            </SectionLabel>

            <div class="col-span-2">
                <Input
                    variant="soft"
                    label="Current password"
                    type="password"
                    name="current_password"
                    autocomplete="current-password"
                    required
                />
                {@render fieldError(errors.current_password)}
            </div>

            <div class="col-span-2">
                <Input
                    variant="soft"
                    label="New password"
                    type="password"
                    name="password"
                    autocomplete="new-password"
                    required
                />
                {@render fieldError(errors.password)}
            </div>

            <div class="col-span-2">
                <Input
                    variant="soft"
                    label="Confirm new password"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    required
                />
            </div>

            <Button
                type="submit"
                variant="primary"
                label="Change password"
                icon={KeyRound}
                loading={processing}
                class="col-span-2 w-full"
            />
        {/snippet}
    </Form>
</section>
