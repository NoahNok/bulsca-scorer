<script module lang="ts">
    import { index } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { UserRound } from "@lucide/svelte";

    export const layout = {
        header: { icon: UserRound, href: index() },
    };
</script>

<script lang="ts">
    import {
        destroyPhoto,
        devices,
        updateAccount,
        updatePassword,
        updatePhoto,
    } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/AccountController";
    import AppHead from "@/components/AppHead.svelte";
    import Avatar from "@/components/Avatar.svelte";
    import BackLink from "@/components/BackLink.svelte";
    import Button from "@/components/Button.svelte";
    import ConfirmDialog from "@/components/ConfirmDialog.svelte";
    import Input from "@/components/input.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";
    import { downscaleImage } from "@/lib/image";
    import { toastError } from "@/lib/toast.svelte";
    import type { ProfilePhoto } from "@/types/presence";
    import { Form, Link, router } from "@inertiajs/svelte";
    import {
        Camera,
        Check,
        ChevronRight,
        KeyRound,
        MonitorSmartphone,
    } from "@lucide/svelte";
    import { slide } from "svelte/transition";

    let {
        name,
        email,
        photo,
        deviceCount,
    }: {
        name: string;
        email: string;
        photo: ProfilePhoto | null;
        deviceCount: number;
    } = $props();

    let photoInput: HTMLInputElement;
    let photoProcessing = $state(false);

    const photoOptions = {
        preserveScroll: true,
        onError: (errors: Record<string, string>) =>
            toastError(errors.photo ?? "Couldn't update your photo"),
        onFinish: () => (photoProcessing = false),
    };

    async function uploadPhoto() {
        const file = photoInput.files?.[0];
        photoInput.value = "";
        if (!file) return;

        if (!file.type.startsWith("image/")) {
            toastError("Choose an image file");
            return;
        }

        photoProcessing = true;
        const blob = await downscaleImage(file);

        if (blob.size > 10 * 1024 * 1024) {
            photoProcessing = false;
            toastError("That photo is too big", "Choose one under 10 MB.");
            return;
        }

        router.post(
            updatePhoto(),
            { photo: new File([blob], "photo.jpg", { type: blob.type }) },
            { ...photoOptions, forceFormData: true },
        );
    }

    function removePhoto() {
        photoProcessing = true;
        router.delete(destroyPhoto(), photoOptions);
    }
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

    <Link
        href={devices()}
        class="group mt-3 flex items-center gap-3 rounded-xl border bg-white p-3 shadow-sm hover:border-se hover:shadow-md"
    >
        <span
            class="flex size-8 shrink-0 items-center justify-center rounded-md bg-gray-100 text-gray-600 group-hover:bg-se/20"
        >
            <MonitorSmartphone size={16} />
        </span>
        <span class="flex-1">
            <span class="font-archivo block text-sm">Signed-in devices</span>
            <span class="block text-xs text-gray-500">
                {deviceCount === 1 ? "1 device" : `${deviceCount} devices`}
            </span>
        </span>
        <ChevronRight size={16} class="text-gray-400" />
    </Link>

    <SectionLabel class="mt-6 mb-2">Photo</SectionLabel>

    <div class="flex items-center gap-4 rounded-xl border bg-white p-3">
        <Avatar {name} {photo} class="size-16 text-xl" />
        <div class="flex flex-1 flex-wrap justify-end gap-2">
            {#if photo}
                <ConfirmDialog
                    title="Remove photo"
                    description="Other officials will see your initials instead."
                    triggerLabel="Remove"
                    triggerVariant="white"
                    confirmLabel="Remove"
                    loading={photoProcessing}
                    onConfirm={removePhoto}
                />
            {/if}
            <Button
                type="button"
                variant="secondary"
                label={photo ? "Change" : "Upload photo"}
                icon={Camera}
                loading={photoProcessing}
                onclick={() => photoInput.click()}
            />
        </div>
        <input
            bind:this={photoInput}
            type="file"
            accept="image/jpeg,image/png,image/webp"
            class="hidden"
            onchange={uploadPhoto}
        />
    </div>
    <p class="mt-1 text-xs text-gray-500">
        Shown to other officials at your competitions.
    </p>

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
