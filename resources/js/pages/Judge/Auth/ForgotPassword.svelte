<script module lang="ts">
    import { KeyRound } from "@lucide/svelte";

    export const layout = {
        header: { icon: KeyRound, overlay: true },
    };
</script>

<script lang="ts">
    import {
        forgotPassword,
        forgotPasswordPost,
    } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController";
    import { login } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/LoginController";
    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import Input from "@/components/input.svelte";
    import { toastError, toastSuccess } from "@/lib/toast.svelte";
    import { Form, Link, router } from "@inertiajs/svelte";
    import { ArrowLeft, ArrowRight, Mail, RotateCw } from "@lucide/svelte";
    import { slide } from "svelte/transition";

    let {
        stage = "email",
        email,
    }: {
        stage?: "email" | "pin";
        email?: string;
    } = $props();

    let resending = $state(false);

    // posting just the email sends a fresh PIN and re-renders this stage
    function resendPin() {
        router.post(
            forgotPasswordPost(),
            { email, resend: 1 },
            {
                preserveScroll: true,
                onStart: () => (resending = true),
                onFinish: () => (resending = false),
                onSuccess: () =>
                    toastSuccess("New PIN sent", `Check your email`),
                onError: (errors) =>
                    toastError("Couldn't send a new PIN", errors.pin),
            },
        );
    }
</script>

<AppHead title="Reset password" />

{#snippet fieldError(error: string | undefined)}
    {#if error}
        <p transition:slide={{ duration: 100 }} class="mt-1 text-xs text-red-600">
            {error}
        </p>
    {/if}
{/snippet}

<section class="flex h-full flex-col justify-center">
    <div class="mx-auto flex w-full max-w-sm flex-col">
        <p class="font-archivo -mb-2">Officials</p>
        <h2>Reset password</h2>

        {#if stage === "email"}
            <p class="mt-1 text-sm text-gray-500">
                Enter your email and we'll send you a 6-digit PIN to set a new
                password.
            </p>

            <Form action={forgotPasswordPost()} class="mt-6 grid gap-3">
                {#snippet children({ errors, processing })}
                    <div>
                        <Input
                            variant="soft"
                            label="Email"
                            type="email"
                            name="email"
                            value={email ?? ""}
                            placeholder="judge@scoring.events"
                            autocomplete="email"
                            required
                        />
                        {@render fieldError(errors.email)}
                    </div>

                    <Button
                        type="submit"
                        variant="primary"
                        label="Send PIN"
                        icon={ArrowRight}
                        iconClass="transition-transform group-hover:translate-x-0.5"
                        loading={processing}
                        class="mt-1 w-full"
                    />
                {/snippet}
            </Form>
        {:else}
            <p class="mt-1 text-sm text-gray-500">
                If there's an account for this email, we've sent it a PIN. Enter
                it with your new password.
            </p>

            <div class="mt-3 flex items-center gap-3 rounded-xl bg-gray-50 p-3">
                <span
                    class="flex size-8 shrink-0 items-center justify-center rounded-md bg-se/20 text-teal-700"
                >
                    <Mail size={16} />
                </span>
                <div class="min-w-0 flex-1">
                    <p class="text-xs text-gray-500">PIN sent to</p>
                    <p class="truncate text-sm font-semibold text-gray-900">
                        {email}
                    </p>
                </div>
                <Link
                    href={forgotPassword()}
                    class="shrink-0 text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                >
                    Change
                </Link>
            </div>

            <Form
                action={forgotPasswordPost()}
                class="mt-6 grid gap-3"
                resetOnError={["password", "password_confirmation"]}
            >
                {#snippet children({ errors, processing })}
                    <input type="hidden" name="email" value={email} />
                    <div>
                        <!-- no auto-submit: the password fields come after it -->
                        <Input
                            variant="soft"
                            label="6-digit PIN"
                            type="text"
                            name="pin"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            pattern={"\\d{6}"}
                            maxlength={6}
                            placeholder="••••••"
                            required
                            class="text-center font-mono text-xl! tracking-[0.5em]"
                            {@attach (el: HTMLElement) => el.focus()}
                            oninput={(e: Event & { currentTarget: HTMLInputElement }) => {
                                const input = e.currentTarget;
                                input.value = input.value.replace(/\D/g, "").slice(0, 6);
                            }}
                        />
                        {@render fieldError(errors.pin)}
                    </div>

                    <div>
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

                    <Input
                        variant="soft"
                        label="Confirm new password"
                        type="password"
                        name="password_confirmation"
                        autocomplete="new-password"
                        required
                    />

                    <Button
                        type="submit"
                        variant="primary"
                        label="Reset password"
                        icon={ArrowRight}
                        iconClass="transition-transform group-hover:translate-x-0.5"
                        loading={processing}
                        class="mt-1 w-full"
                    />
                {/snippet}
            </Form>

            <button
                type="button"
                class="mt-3 inline-flex cursor-pointer items-center gap-1.5 self-center text-sm text-gray-600 hover:text-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se disabled:cursor-default disabled:text-gray-400"
                disabled={resending}
                onclick={resendPin}
            >
                <RotateCw size={14} class={resending ? "animate-spin" : ""} />
                Didn't get it? Send a new PIN
            </button>
        {/if}

        <Link
            href={login()}
            class="mt-6 inline-flex items-center gap-1.5 self-center text-sm text-gray-600 hover:text-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
        >
            <ArrowLeft size={14} />
            Back to sign in
        </Link>
    </div>
</section>
