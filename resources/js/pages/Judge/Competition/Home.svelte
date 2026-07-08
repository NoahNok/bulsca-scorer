<script module lang="ts">
    export const layout = {
        title: "Sign in to your account",
        description: "Enter your email and password below to sign in",
    };
</script>

<script lang="ts">
    import {
        index,
        confirmJudge,
        home,
    } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";

    import AppHead from "@/components/AppHead.svelte";
    import Button from "@/components/Button.svelte";
    import Input from "@/components/input.svelte";
    import { appState } from "@/lib/stores/appState";
    import { toastSuccess } from "@/lib/toast.svelte";
    import { event } from "@/routes/live/dqs";
    import type { Competition, Event, SERC } from "@/types/base";
    import { Form, page, Link } from "@inertiajs/svelte";
    import {
        ArrowRight,
        Check,
        Clipboard,
        FingerprintPattern,
        Home,
        House,
        Plus,
        PlusCircle,
    } from "@lucide/svelte";
    import { slide } from "svelte/transition";

    const user = $derived(page.props.auth.user);

    let {
        competition,
        sercs,
        speeds,
    }: {
        competition: Competition;
        sercs: SERC[];
        speeds: Event[];
    } = $props();

    $effect(() => {
        $appState.activeCompetition = competition;
    });

    const isHead = !false;
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
    <p class="font-archivo -mb-2">Welcome to,</p>
    <h2 class="">{competition.name}</h2>
    <br />

    <div class="flex flex-col space-y-3">
        {#each sercs as serc}
            <div class="mb-5">
                {#if isHead}
                    <div class="flex items-center justify-between space-x-3">
                        <h3>{serc.name}</h3>

                        {#if serc.completed}
                            <span
                                class="tooltip-left mr-1"
                                title="Event Complete"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    class="size-6 text-green-500"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                        {:else}
                            <span
                                class="tooltip-left mr-1"
                                title="Event Incomplete"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    class="size-6 text-red-500"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                        {/if}
                    </div>
                {:else}
                    <h3>{serc.name}</h3>
                {/if}

                {#each serc.judges as judge}
                    <Link
                        href={confirmJudge({
                            competition: competition,
                            judge: judge,
                        })}
                        class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1"
                    >
                        <p class="font-archivo">{judge.name}</p>

                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m8.25 4.5 7.5 7.5-7.5 7.5"
                            ></path>
                        </svg>
                    </Link>
                {/each}

                <a
                    href="#issue-dq"
                    class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1"
                >
                    <p class="font-archivo">Issue DQ/Penalty</p>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002"
                        />
                    </svg>
                </a>

                {#if isHead}
                    <hr class="spacer" />
                    {#if serc.confirmed}
                        <Button
                            label="Confirmed"
                            variant="success"
                            icon={Check}
                            class="w-full py-1 px-2 pointer-events-none"
                        />
                    {:else}
                        <a
                            href="#confirm-route"
                            onclick={() => toastSuccess("test")}
                            class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1"
                        >
                            <p class="font-archivo">Confirm Results</p>

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                                />
                            </svg>
                        </a>
                    {/if}
                {/if}
            </div>
        {/each}

        <hr class="spacer !mt-2" />
        <br />

        {#each speeds as speed}
            <div class="mb-5">
                {#if isHead}
                    <div class="flex items-center justify-between space-x-3">
                        <h3>{speed.name}</h3>

                        {#if speed.completed}
                            <span
                                class="tooltip-left pr-1"
                                title="Event Complete"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    class="size-6 text-green-500"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                        {:else}
                            <span
                                class="tooltip-left pr-1"
                                title="Event Incomplete"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="currentColor"
                                    class="size-6 text-red-500"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                        {/if}
                    </div>
                {:else}
                    <h3>{speed.name}</h3>
                {/if}

                <a
                    href="#time-entry"
                    class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1"
                >
                    <p class="font-archivo">Times</p>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m8.25 4.5 7.5 7.5-7.5 7.5"
                        ></path>
                    </svg>
                </a>

                <a
                    href="#oof-entry"
                    class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1"
                >
                    <p class="font-archivo">Order of Finish</p>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m8.25 4.5 7.5 7.5-7.5 7.5"
                        ></path>
                    </svg>
                </a>

                <a
                    href="#issue-dq"
                    class="flex items-center cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1"
                >
                    <p class="font-archivo">Issue DQ/Penalty</p>

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002"
                        />
                    </svg>
                </a>

                {#if isHead}
                    <hr class="spacer" />
                    {#if speed.confirmed}
                        <Button
                            label="Confirmed"
                            variant="success"
                            icon={Check}
                            class="w-full py-1 px-2 pointer-events-none"
                        />
                    {:else}
                        <a
                            href="#confirm-speed"
                            class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1"
                        >
                            <p class="font-archivo">Confirm Results</p>

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                                />
                            </svg>
                        </a>
                    {/if}
                {/if}
            </div>
        {/each}

        <!-- @foreach ($comp->getSpeedEvents->where('digitalJudgeEnabled') as $event)
            <div class="mb-5">


                @if ($head)
                    <div class="flex items-center justify-between space-x-3">
                        <h3>{{ $event->getName() }}</h3>
                        @if ($event->completed || $event->isComplete())
                            <span class="tooltip-left" title="Event Complete">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-6 text-green-500" title="ahh">
                                    <path fill-rule="evenodd"
                                        d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        @else
                            <span class="tooltip" title="Event Incomplete">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="size-6 text-red-500">
                                    <path fill-rule="evenodd"
                                        d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z"
                                        clip-rule="evenodd" />
                                </svg>


                            </span>
                        @endif
                    </div>
                @else
                    <h3>{{ $event->getName() }}</h3>
                @endif

                <a href="{{ route('dj.speeds.times.index', $event) }}"
                    class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">Times</p>



                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
                    </svg>



                </a>

                <a href="{{ route('dj.speeds.oof.index', $event) }}"
                    class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">Order of Finish</p>



                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path>
                    </svg>



                </a>



                <a href="{{ route('dj.dq.issue') }}?event=sp:{{ $event->id }}"
                    class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                    <p class="font-archivo">Issue DQ/Penalty</p>


                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.05 4.575a1.575 1.575 0 1 0-3.15 0v3m3.15-3v-1.5a1.575 1.575 0 0 1 3.15 0v1.5m-3.15 0 .075 5.925m3.075.75V4.575m0 0a1.575 1.575 0 0 1 3.15 0V15M6.9 7.575a1.575 1.575 0 1 0-3.15 0v8.175a6.75 6.75 0 0 0 6.75 6.75h2.018a5.25 5.25 0 0 0 3.712-1.538l1.732-1.732a5.25 5.25 0 0 0 1.538-3.712l.003-2.024a.668.668 0 0 1 .198-.471 1.575 1.575 0 1 0-2.228-2.228 3.818 3.818 0 0 0-1.12 2.687M6.9 7.575V12m6.27 4.318A4.49 4.49 0 0 1 16.35 15m.002 0h-.002" />
                    </svg>



                </a>

                @if ($head)
                    <hr class="spacer">
                    @if ($event->digitalJudgeConfirmed)
                        <span class="flex items-center bg-green-500 rounded-md px-2 py-1 text-white">
                            <p class=" font-archivo   ">Confirmed</p>


                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                                stroke="currentColor" class="ml-auto size-4  transition-all ">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>




                        </span>
                    @else
                        <a href="{{ route('dj.confirm-results.speed', $event) }}"
                            class="flex items-center
                    cursor-pointer transition-colors group hover:bg-gray-200 rounded-md px-2 py-1">
                            <p class="font-archivo">Confirm Results</p>

                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="ml-auto size-4 group-hover:text-se transition-all group-hover:stroke-3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>


                        </a>
                    @endif
                @endif


            </div>
        @endforeach -->
    </div>
</section>
