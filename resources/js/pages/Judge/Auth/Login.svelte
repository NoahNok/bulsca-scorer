<script module lang="ts">
    import { FingerprintPattern } from "@lucide/svelte";

    export const layout = {
        header: { icon: FingerprintPattern, overlay: true },
    };
</script>

<script lang="ts">
    import { forgotPassword } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/ForgotPasswordController";
    import {
        login,
        loginPassword,
        loginPost,
    } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/LoginController";
    import { register } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/RegisterController";
    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import Input from "@/components/input.svelte";
    import { toastError, toastSuccess } from "@/lib/toast.svelte";
    import { Form, Link, router } from "@inertiajs/svelte";
    import {
        ArrowRight,
        KeyRound,
        Mail,
        RotateCw,
        UserPlus,
    } from "@lucide/svelte";
    import { untrack } from "svelte";
    import { slide } from "svelte/transition";

    type LoginStage = "email" | "pin";

    let {
        stage = "email",
        email,
        registered = false,
    }: {
        stage?: LoginStage;
        email?: string;
        // just signed up, so the PIN confirms their email
        registered?: boolean;
    } = $props();

    let resending = $state(false);

    // officials can skip the emailed PIN if they know their password
    let method = $state<"pin" | "password">("pin");
    // kept in state so it survives switching method
    let emailInput = $state(untrack(() => email) ?? "");

    // posting just the email sends a fresh PIN and re-renders this stage
    function resendPin() {
        router.post(
            loginPost(),
            { email, resend: 1, registered: registered ? 1 : 0 },
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

<AppHead title={registered ? "Confirm your email" : "Sign in"} />

<section class="flex h-full flex-col justify-center">
    <div class="mx-auto flex w-full max-w-sm flex-col">
        <p class="font-archivo -mb-2">Officials</p>
        <h2>
            {stage === "pin"
                ? registered
                    ? "Confirm your email"
                    : "Enter your PIN"
                : "Sign in"}
        </h2>

        {#if stage === "email"}
            <p class="mt-1 text-sm text-gray-500">
                {#if method === "pin"}
                    Use the email address you were registered with. We'll email
                    you a 6-digit PIN.
                {:else}
                    Sign in with your email and password.
                {/if}
            </p>

            <Form
                action={method === "pin" ? loginPost() : loginPassword()}
                class="mt-6 grid gap-3"
            >
                {#snippet children({ errors, processing })}
                    <div>
                        <Input
                            variant="soft"
                            label="Email"
                            type="email"
                            name="email"
                            bind:value={emailInput}
                            placeholder="judge@scoring.events"
                            autocomplete="email"
                            required
                        />
                        {#if errors.email}
                            <p
                                transition:slide={{ duration: 100 }}
                                class="mt-1 text-xs text-red-600"
                            >
                                {errors.email}
                            </p>
                        {/if}
                    </div>

                    {#if method === "password"}
                        <div transition:slide={{ duration: 150 }}>
                            <input type="hidden" name="remember" value="1" />
                            <Input
                                variant="soft"
                                label="Password"
                                type="password"
                                name="password"
                                autocomplete="current-password"
                                required
                            />
                            {#if errors.password}
                                <p
                                    transition:slide={{ duration: 100 }}
                                    class="mt-1 text-xs text-red-600"
                                >
                                    {errors.password}
                                </p>
                            {/if}
                            <Link
                                href={forgotPassword()}
                                class="mt-1.5 inline-block text-xs text-gray-600 underline hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                            >
                                Forgot password?
                            </Link>
                        </div>
                    {/if}

                    <Button
                        type="submit"
                        variant="primary"
                        label={method === "pin" ? "Send PIN" : "Sign in"}
                        icon={ArrowRight}
                        iconClass="transition-transform group-hover:translate-x-0.5"
                        loading={processing}
                        class="mt-1 w-full"
                    />
                {/snippet}
            </Form>

            <button
                type="button"
                class="mt-3 inline-flex cursor-pointer items-center gap-1.5 self-center text-sm text-gray-600 hover:text-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                onclick={() => (method = method === "pin" ? "password" : "pin")}
            >
                {#if method === "pin"}
                    <KeyRound size={14} />
                    Sign in with a password instead
                {:else}
                    <Mail size={14} />
                    Email me a PIN instead
                {/if}
            </button>

            <Link
                href={register()}
                class="mt-6 inline-flex items-center gap-1.5 self-center text-sm text-gray-600 hover:text-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
            >
                <UserPlus size={14} />
                New official? Create an account
            </Link>
        {:else}
            {#if registered}
                <p class="mt-1 text-sm text-gray-500">
                    We've emailed you a 6-digit PIN to finish creating your
                    account.
                </p>
            {/if}
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
                    href={login()}
                    class="shrink-0 text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                >
                    Change
                </Link>
            </div>

            <Form action={loginPost()} class="mt-6 grid gap-3">
                {#snippet children({ errors, processing, submit })}
                    <input type="hidden" name="email" value={email} />
                    {#if registered}
                        <input type="hidden" name="registered" value="1" />
                    {/if}
                    <div>
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
                                // digits only, and submit as soon as the PIN is complete
                                const input = e.currentTarget;
                                input.value = input.value.replace(/\D/g, "").slice(0, 6);
                                if (input.value.length === 6 && !processing) {
                                    submit();
                                }
                            }}
                        />
                        {#if errors.pin}
                            <p
                                transition:slide={{ duration: 100 }}
                                class="mt-1 text-xs text-red-600"
                            >
                                {errors.pin}
                            </p>
                        {/if}
                    </div>

                    <Button
                        type="submit"
                        variant="primary"
                        label="Sign in"
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
    </div>
</section>
