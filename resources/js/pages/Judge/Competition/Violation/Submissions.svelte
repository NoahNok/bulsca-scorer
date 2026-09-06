<script module lang="ts">
    export const layout = {
        title: "Confirm Judge",
    };
</script>

<script lang="ts">
    import { home } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import AppHead from "@/components/AppHead.svelte";

    import type { Competition } from "@/types/base";
    import { page, Link } from "@inertiajs/svelte";
    import { House } from "@lucide/svelte";

    const user = $derived(page.props.auth.user);

    let {
        competition,
    }: {
        competition: Competition;
    } = $props();
</script>

<AppHead title="Submissions - DQ/Penalty - {competition.name}" />

<section class="flex flex-col absolute top-0 left-0 w-full p-6 z-10">
    <Link
        href={home(competition)}
        class="flex w-full justify-between items-center"
    >
        <div class="">
            <h1 class="  -mb-3 normal-case! text-black! text-base!">Digital</h1>
            <h1 class=" indent-6 normal-case! text-se text-xl!">Judge</h1>
        </div>

        <House class="bg-se/20 rounded-full text-se p-2 shadow-md " size={40} />
    </Link>
</section>

<div class="h-16"></div>

<section class="flex flex-col h-full">
    <p class="font-archivo -mb-2">{competition.name}</p>
    <h2 class="">DQ/Penalty</h2>
    <br />
</section>
