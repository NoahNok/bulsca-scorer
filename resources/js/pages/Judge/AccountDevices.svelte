<script module lang="ts">
    import { index } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { UserRound } from "@lucide/svelte";

    export const layout = {
        header: { icon: UserRound, href: index() },
    };
</script>

<script lang="ts">
    import {
        account,
        signOutDevice,
        signOutEverywhere,
        signOutOtherDevices,
    } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/AccountController";
    import AppHead from "@/components/AppHead.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import ConfirmDialog from "@/components/ConfirmDialog.svelte";
    import EmptyState from "@/components/EmptyState.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";
    import { router } from "@inertiajs/svelte";
    import { LogOut, Monitor, Smartphone, Tablet } from "@lucide/svelte";
    import { flip } from "svelte/animate";

    type Device = {
        id: number;
        type: string;
        name: string;
        browser: string;
        ip: string | null;
        lastActive: string;
        signedIn: string | null;
        current: boolean;
        remembered: boolean;
    };

    let { devices }: { devices: Device[] } = $props();

    let current = $derived(devices.find((d) => d.current));
    let others = $derived(devices.filter((d) => !d.current));

    let processing = $state(false);

    const icons: Record<string, any> = {
        mobile: Smartphone,
        tablet: Tablet,
    };

    function send(action: () => void) {
        processing = true;
        action();
    }

    const options = {
        preserveScroll: true,
        onFinish: () => (processing = false),
    };
</script>

<AppHead title="Signed-in devices" />

{#snippet deviceRow(device: Device)}
    {@const Icon = icons[device.type] ?? Monitor}
    <div class="flex items-center gap-3 p-3">
        <span
            class="flex size-9 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-600"
        >
            <Icon size={18} />
        </span>
        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold">{device.name}</p>
            <p class="truncate text-xs text-gray-500">
                {device.browser || "Unknown browser"}{#if device.ip}
                    · {device.ip}{/if}
            </p>
            <p class="text-xs text-gray-400">
                {device.current ? "Active now" : `Last active ${device.lastActive}`}{#if device.signedIn}
                    · signed in {device.signedIn}{/if}
            </p>
        </div>
        {#if !device.current}
            <ConfirmDialog
                title="Sign out {device.name}"
                description="This device will need to sign in again."
                triggerLabel="Sign out"
                triggerVariant="white"
                confirmLabel="Sign out"
                loading={processing}
                onConfirm={() =>
                    send(() =>
                        router.delete(signOutDevice(device.id), options),
                    )}
            />
        {/if}
    </div>
{/snippet}

<section class="flex flex-col">
    <p class="font-archivo -mb-2">Officials</p>
    <h2>Signed-in devices</h2>

    <BackLink href={account()} label="Your account" class="mt-2 mb-4" />

    {#if current}
        <SectionLabel class="mb-1">This device</SectionLabel>
        <div class="rounded-xl border bg-white">
            {@render deviceRow(current)}
        </div>
    {/if}

    <SectionLabel class="mt-6 mb-1">Other devices</SectionLabel>
    {#if others.length}
        <div class="divide-y rounded-xl border bg-white">
            {#each others as device (device.id)}
                <div animate:flip={{ duration: 200 }}>
                    {@render deviceRow(device)}
                </div>
            {/each}
        </div>
    {:else}
        <EmptyState
            icon={Smartphone}
            title="No other devices"
            description="You're only signed in here."
        />
    {/if}

    <div class="mt-6 flex flex-col gap-2">
        {#if others.length}
            <ConfirmDialog
                title="Sign out other devices"
                description="Every device except this one will need to sign in again."
                triggerLabel="Sign out all other devices"
                triggerVariant="secondary"
                triggerClass="w-full"
                triggerIcon={LogOut}
                confirmLabel="Sign out"
                loading={processing}
                onConfirm={() =>
                    send(() =>
                        router.delete(signOutOtherDevices(), options),
                    )}
            />
        {/if}
        <ConfirmDialog
            title="Sign out everywhere"
            description="Every device, including this one, will need to sign in again."
            triggerLabel="Sign out everywhere"
            triggerVariant="danger"
            triggerClass="w-full"
            triggerIcon={LogOut}
            confirmLabel="Sign out everywhere"
            loading={processing}
            onConfirm={() =>
                send(() => router.post(signOutEverywhere(), {}, options))}
        />
    </div>
</section>
