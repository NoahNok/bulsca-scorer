<script module lang="ts">
    import { Gavel } from "@lucide/svelte";

    export const layout = {
        header: { icon: Gavel, href: null, overlay: true },
    };
</script>

<script lang="ts">
    import { index as legacyIndex } from "@/actions/App/Http/Controllers/DigitalJudge/DigitalJudgeController";
    import { index } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import { confirm } from "@/lib/confirm";
    import { router } from "@inertiajs/svelte";
    import { ArrowRight, History } from "@lucide/svelte";

    let starting = $state(false);

    async function useLegacy() {
        const ok = await confirm({
            title: "Use Legacy Digital Judge",
            description:
                "Legacy Digital Judge is being phased out, and will recieve no further updates. You should only continue if you have been told to.",
            confirmLabel: "Use Legacy",
            confirmVariant: "primary",
        });

        // Legacy is a Blade app, so leave Inertia with a full page load
        if (ok) window.location.assign(legacyIndex.url());
    }
</script>

<AppHead title="Start judging" />

<section class="flex h-full flex-col justify-center">
    <div class="mx-auto flex w-full max-w-sm flex-col">
        <p class="font-archivo -mb-2">Officials</p>
        <h2>Start judging</h2>
        <p class="mt-1 text-sm text-gray-500">
            Sign in with your account, then join a competition with its PIN.
        </p>

        <Button
            type="button"
            variant="primary"
            label="Start judging"
            icon={ArrowRight}
            iconClass="transition-transform group-hover:translate-x-0.5"
            loading={starting}
            class="mt-6 w-full"
            onclick={() =>
                router.visit(index(), {
                    onStart: () => (starting = true),
                    onFinish: () => (starting = false),
                })}
        />

        <button
            type="button"
            class="mt-3 inline-flex cursor-pointer items-center gap-1.5 self-center text-sm text-gray-600 hover:text-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
            onclick={useLegacy}
        >
            <History size={14} />
            Use Legacy Digital Judge
        </button>

        <p class="mt-10 text-center text-xs text-gray-500">
            Personal devices are used at <strong>your own risk</strong>.
            Scoring.Events, the event host and affiliated parties assume no
            responsibility for any <strong>loss, damage, or malfunction</strong>
            of devices during or as a result of participating in this event.
        </p>
    </div>
</section>
