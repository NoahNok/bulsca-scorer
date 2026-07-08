<script lang="ts">
    import { loginPost } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import AppHead from "@/components/AppHead.svelte";
    import Input from "@/components/input.svelte";
    import { Form } from "@inertiajs/svelte";
    import { ArrowRight, FingerprintPattern } from "@lucide/svelte";
    import { fade, slide } from "svelte/transition";

    type LoginStage = "email" | "pin";

    let {
        stage = "email",
        email,
    }: {
        stage?: LoginStage;
        email?: string;
    } = $props();

    let pin = $state("");
</script>

<AppHead title="Sign in" />

<section class="flex flex-col fixed top-0 left-0 w-full p-6 z-10">
    <div class="flex w-full justify-between items-center">
        <div class="">
            <h1 class="  -mb-3 normal-case! text-black! text-base!">Digital</h1>
            <h1 class=" indent-6 normal-case! text-se text-xl!">Judge</h1>
        </div>

        <FingerprintPattern
            class="bg-se/20 rounded-full text-se p-1.5  shadow-md "
            size={40}
        />
    </div>
</section>

<section class="flex flex-col items-center h-full justify-center -mt-6!">
    <div class="my-12! flex flex-col items-center space-y-3 relative">
        <FingerprintPattern
            class="bg-se/20 rounded-full text-se p-3  shadow-md  "
            size={64}
        />
        <h3 class=" capitalize!">Sign-in</h3>
    </div>

    <Form
        {loginPost}
        method="post"
        class="flex flex-col w-[90%] items-center space-y-3!"
    >
        {#snippet children({ errors, processing })}
            {#if stage === "email"}
                <Input
                    placeholder="judge@scoring.events"
                    type="email"
                    name="email"
                    class="  "
                />
                {#if errors.email}
                    <small
                        transition:slide={{ duration: 100, y: 6 }}
                        class="text-red-500 self-end! -mt-2!"
                        >{errors.email}</small
                    >
                {/if}

                <button
                    class="bg-se/10 text-se py-2 px-4 rounded-lg font-semibold inline-flex items-center gap-2 ml-auto hover:bg-se hover:text-white transition-all group cursor-pointer shadow-md"
                    >Sign-in <ArrowRight
                        size={16}
                        class="group-hover:translate-x-1 transition-transform mt-0.5"
                    />
                </button>
            {:else if stage === "pin"}
                <small>Enter the 6-digit PIN sent to your email</small>
                <input type="hidden" name="email" value={email} />
                <Input
                    placeholder="Enter your PIN"
                    type="number"
                    name="pin"
                    class="  "
                />
                {#if errors.pin}
                    <small
                        transition:slide={{ duration: 100, y: 6 }}
                        class="text-red-500 self-end! -mt-2!"
                        >{errors.pin}</small
                    >
                {/if}

                <button
                    class="bg-se/10 text-se py-2 px-4 rounded-lg font-semibold inline-flex items-center gap-2 ml-auto hover:bg-se hover:text-white transition-all group cursor-pointer shadow-md"
                    >Continue <ArrowRight
                        size={16}
                        class="group-hover:translate-x-1 transition-transform mt-0.5"
                    />
                </button>
            {/if}
        {/snippet}
    </Form>
</section>
