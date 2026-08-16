<script module lang="ts">
    export const layout = {
        title: "Confirm Judge",
    };
</script>

<script lang="ts">
    import {
        index,
        home,
    } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import { confirmJudgePost } from "@/actions/App/Http/Controllers/DigitalJudge/SERC/SERCJudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";

    import { appState } from "@/lib/stores/appState";

    import type { Competition, Judge, SERC } from "@/types/base";
    import { Form, page, Link } from "@inertiajs/svelte";
    import { Check, Clipboard, House } from "@lucide/svelte";

    const user = $derived(page.props.auth.user);

    let {
        competition,
        serc,
        judge,
    }: {
        competition: Competition;
        serc: SERC;
        judge: Judge;
    } = $props();
</script>

<AppHead title="Dashboard" />

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
    <h2 class="">{serc.name}</h2>
    <br />

    <div class="flex flex-col space-y-3">
        <h2 class="font-bold text-center w-full break-words">{judge.name}</h2>

        <p class="">
            Please check the judging criteria below matches your brief and then
            click "Continue".
        </p>

        <h4>Criteria</h4>
        <ul class=" list-disc list-inside">
            {#each judge.marking_points as marking_point}
                <li>{marking_point.description}</li>
            {/each}
        </ul>
        <br />

        <Form
            action={confirmJudgePost({
                competition: competition,
                serc: serc,
            })}
        >
            <Button
                label="Continue"
                variant="success"
                icon={Check}
                class="w-full"
            />

            <input type="hidden" name="judge" value={judge.id} />
        </Form>

        <!-- <form action="{{ route('dj.judging.confirm', $judge) }}" method="post" class="w-full ">
            @csrf
            <button type="submit" class="se-btn se-btn-success w-full">Continue</button>
        </form> -->
        <Link href={home(competition)}
            ><Button label="Back" variant="danger" class="w-full py-1!" /></Link
        >
    </div>
</section>
