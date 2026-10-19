<script module lang="ts">
    import { UserPlus } from "@lucide/svelte";

    export const layout = {
        header: { icon: UserPlus, overlay: true },
    };
</script>

<script lang="ts">
    import { login } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/LoginController";
    import { registerPost } from "@/actions/App/Http/Controllers/DigitalJudge/Auth/RegisterController";
    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import Input from "@/components/input.svelte";
    import LegalNotice from "@/components/LegalNotice.svelte";
    import { Form, Link } from "@inertiajs/svelte";
    import { ArrowRight, LogIn } from "@lucide/svelte";
    import { slide } from "svelte/transition";
</script>

<AppHead title="Create account" />

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
        <h2>Create account</h2>

        <p class="mt-1 text-sm text-gray-500">
            We'll email you a 6-digit PIN to confirm your address.
        </p>

        <Form
            action={registerPost()}
            class="mt-6 grid gap-3"
            resetOnError={["password", "password_confirmation"]}
        >
            {#snippet children({ errors, processing })}
                <div>
                    <Input
                        variant="soft"
                        label="Name"
                        name="name"
                        autocomplete="name"
                        placeholder="Your full name"
                        required
                    />
                    {@render fieldError(errors.name)}
                </div>

                <div>
                    <Input
                        variant="soft"
                        label="Email"
                        type="email"
                        name="email"
                        placeholder="judge@scoring.events"
                        autocomplete="email"
                        required
                    />
                    {@render fieldError(errors.email)}
                </div>

                <div>
                    <Input
                        variant="soft"
                        label="Password"
                        type="password"
                        name="password"
                        autocomplete="new-password"
                        required
                    />
                    {@render fieldError(errors.password)}
                </div>

                <Input
                    variant="soft"
                    label="Confirm password"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password"
                    required
                />

                <Button
                    type="submit"
                    variant="primary"
                    label="Create account"
                    icon={ArrowRight}
                    iconClass="transition-transform group-hover:translate-x-0.5"
                    loading={processing}
                    class="mt-1 w-full"
                />
            {/snippet}
        </Form>

        <Link
            href={login()}
            class="mt-3 inline-flex items-center gap-1.5 self-center text-sm text-gray-600 hover:text-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
        >
            <LogIn size={14} />
            Already have an account? Sign in
        </Link>

        <LegalNotice class="mt-6" />
    </div>
</section>
