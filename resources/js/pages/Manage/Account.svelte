<!--
    Your account (account), for anyone signed in. A SideMenu like the Settings pages: Details (your name)
    and Security (changing your password, and the devices you're signed in on, each of which you can sign
    out). Leaving a form with unsaved changes asks first.
-->
<script lang="ts">
    import { signOutDevice, signOutOtherDevices, update, updatePassword } from "@/actions/App/Http/Controllers/AccountController";
    import { confirm } from "@/lib/confirm";
    import Alert from "@/manage/components/Alert.svelte";
    import Field from "@/manage/components/Field.svelte";
    import SideMenu, { type SideMenuItem } from "@/manage/components/SideMenu.svelte";
    import SideMenuPart from "@/manage/components/SideMenuPart.svelte";
    import UnsavedChanges from "@/manage/components/UnsavedChanges.svelte";
    import type { Device } from "@/manage/types";
    import { Form, page, router } from "@inertiajs/svelte";

    let {
        user,
        devices,
    }: {
        user: { name: string; email: string };
        devices: Device[];
    } = $props();

    const PARTS: SideMenuItem[] = [
        { id: "details", label: "Details" },
        { id: "security", label: "Security" },
    ];

    async function signOut(device: Device) {
        const ok = await confirm({
            title: `Sign out ${device.name}?`,
            description: "It will need to sign in again.",
            confirmLabel: "Sign out",
            confirmVariant: "danger",
        });
        if (ok) router.delete(signOutDevice(device.id), { preserveScroll: true });
    }

    async function signOutOthers() {
        const ok = await confirm({
            title: "Sign out every other device?",
            description: "Every device except this one will need to sign in again.",
            confirmLabel: "Sign out all",
            confirmVariant: "danger",
        });
        if (ok) router.delete(signOutOtherDevices(), { preserveScroll: true });
    }

    const heading = "text-lg font-extrabold uppercase";
    const lead = "mt-[0.35rem] mb-5 text-[0.9375rem]/[1.6] text-v2-muted";
</script>

<svelte:head>
    <title>Your account - Scoring.Events</title>
</svelte:head>

<div class="mb-8">
    <h1 class="text-[1.75rem] font-extrabold uppercase">Your account</h1>
    <p class="text-v2-muted wrap-anywhere">{user.email}</p>
</div>

<SideMenu items={PARTS} label="Account">
    <SideMenuPart id="details" labelledby="a-details">
        <h2 id="a-details" class={heading}>Details</h2>
        <p class={lead}>Your name, shown to the people you manage competitions with.</p>

        <Form action={update()} options={{ preserveScroll: true }} setDefaultsOnSuccess>
            {#snippet children({ errors, processing, isDirty })}
                <div class="max-w-lg">
                    <Field id="name" name="name" label="Name" required maxlength={255} autocomplete="name" value={user.name} error={errors.name} />
                </div>

                <UnsavedChanges dirty={isDirty} />
                <div class="mt-6 flex items-center justify-end gap-4">
                    {#if isDirty}<span class="text-sm font-semibold text-v2-muted">Unsaved changes</span>{/if}
                    <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Save</button>
                </div>
            {/snippet}
        </Form>
    </SideMenuPart>

    <SideMenuPart id="security" labelledby="a-security">
        <h2 id="a-security" class={heading}>Security</h2>
        <p class={lead}>Your password, and the devices you're signed in on.</p>

        <section aria-labelledby="a-password">
            <h3 id="a-password" class="font-extrabold uppercase">Password</h3>
            <p class="mt-1 mb-4 text-sm text-v2-muted">Changing it signs you out on every other device.</p>

            <Form action={updatePassword()} options={{ preserveScroll: true }} resetOnSuccess>
                {#snippet children({ errors, processing, isDirty })}
                    <div class="max-w-lg">
                        <Field
                            id="current_password"
                            name="current_password"
                            label="Current password"
                            type="password"
                            required
                            autocomplete="current-password"
                            error={errors.current_password}
                        />
                        <Field id="password" name="password" label="New password" type="password" required autocomplete="new-password" error={errors.password} />
                        <Field
                            id="password_confirmation"
                            name="password_confirmation"
                            label="Confirm new password"
                            type="password"
                            required
                            autocomplete="new-password"
                            error={errors.password_confirmation}
                        />
                    </div>

                    <UnsavedChanges dirty={isDirty} />
                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="v2-btn v2-btn-primary" disabled={processing}>Change password</button>
                    </div>
                {/snippet}
            </Form>
        </section>

        <section class="mt-10" aria-labelledby="a-devices">
            <h3 id="a-devices" class="mb-4 font-extrabold uppercase">Signed-in devices</h3>

            {#if page.props.manage?.flash.error}
                <div class="mb-4"><Alert tone="danger">{page.props.manage.flash.error}</Alert></div>
            {/if}

            <ul class="divide-y divide-v2-border rounded-xl border border-v2-border bg-v2-card">
                {#each devices as device (device.id)}
                    <li class="flex items-center gap-3 px-4 py-3">
                        <div class="min-w-0 flex-1">
                            <p class="flex flex-wrap items-center gap-2 font-semibold">
                                <span class="wrap-anywhere">{device.name}</span>
                                {#if device.current}<span class="v2-badge is-live">This device</span>{/if}
                            </p>
                            <p class="truncate text-sm text-v2-muted">
                                {device.browser || "Unknown browser"}{#if device.ip}&nbsp;&middot; {device.ip}{/if}
                            </p>
                            <p class="text-xs text-v2-muted">
                                {device.current ? "Active now" : `Last active ${device.lastActive}`}{#if device.signedIn}&nbsp;&middot; signed in
                                    {device.signedIn}{/if}
                            </p>
                        </div>
                        {#if !device.current}
                            <button type="button" class="v2-btn v2-btn-danger v2-btn-sm" onclick={() => signOut(device)}>Sign out</button>
                        {/if}
                    </li>
                {/each}
            </ul>

            {#if devices.length > 1}
                <div class="mt-4 flex justify-end">
                    <button type="button" class="v2-btn v2-btn-danger" onclick={signOutOthers}>Sign out all other devices</button>
                </div>
            {/if}
        </section>
    </SideMenuPart>
</SideMenu>
