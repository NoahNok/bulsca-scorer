<script lang="ts">
    import { officials as officialsRoute } from "@/actions/App/Http/Controllers/DigitalJudge/JudgeController";
    import Avatar from "@/components/Avatar.svelte";
    import SectionLabel from "@/components/SectionLabel.svelte";
    import { presence } from "@/lib/presence.svelte";
    import type { Competition } from "@/types/base";
    import {
        presenceStatusBadgeClass,
        presenceStatusDotClass,
        presenceStatusLabel,
        type Official,
        type PresenceMember,
    } from "@/types/presence";
    import { useHttp } from "@inertiajs/svelte";
    import { untrack } from "svelte";
    import { Users, X } from "@lucide/svelte";
    import { Dialog } from "bits-ui";
    import { flip } from "svelte/animate";
    import { fade, fly, scale } from "svelte/transition";

    let { competition }: { competition: Competition } = $props();

    // everyone officiating, fetched each time the sheet opens, so anyone not
    // in the presence channel can be listed as offline
    const http = useHttp<{}, Official[]>();
    let officials = $state<Official[]>([]);

    $effect(() => {
        if (!presence.sheetOpen) return;
        const url = officialsRoute(competition).url;
        untrack(() =>
            http.get(url, { onSuccess: (response) => (officials = response) }),
        );
    });

    const offline = $derived(
        officials.filter((o) => !presence.members.some((m) => m.id === o.id)),
    );

    // the row just poked shows "Sent 👋" in place of its status for a moment
    let pokedId = $state<number | null>(null);
    let pokedTimer: ReturnType<typeof setTimeout> | undefined;

    function poke(member: PresenceMember) {
        if (!presence.poke(member.id)) return;

        pokedId = member.id;
        clearTimeout(pokedTimer);
        pokedTimer = setTimeout(() => (pokedId = null), 2000);
    }

    const rowClass = "flex w-full items-center gap-3 px-3 py-2 text-left";
</script>

{#snippet row(member: PresenceMember, isMe: boolean)}
    <Avatar name={member.name} photo={member.photo}>
        <span
            class="absolute -right-0.5 -bottom-0.5 size-3 rounded-full border-2 border-white transition-colors {presenceStatusDotClass(member.status)}"
        ></span>
    </Avatar>
    <span class="flex min-w-0 flex-1 flex-col">
        <span class="truncate text-sm font-medium text-gray-900">
            {member.name}
            {#if isMe}
                <span class="text-gray-500">(You)</span>
            {/if}
        </span>
        <span class="text-xs text-gray-500">{member.role}</span>
    </span>
    {#if pokedId === member.id}
        <span
            class="shrink-0 rounded-full bg-black px-2 py-0.5 text-xs font-semibold text-white"
            in:scale={{ start: 0.6, duration: 150 }}
        >
            Sent 👋
        </span>
    {:else}
        <span
            class="shrink-0 rounded-full px-2 py-0.5 text-xs font-semibold ring-1 ring-inset {presenceStatusBadgeClass(member.status)}"
        >
            {presenceStatusLabel[member.status]}
        </span>
    {/if}
{/snippet}

<!-- Slide-out list of the competition's officials, online first, opened from the Online tab -->
<Dialog.Root bind:open={presence.sheetOpen}>
    <Dialog.Portal>
        {#if presence.sheetOpen}
            <div
                transition:fade={{ duration: 150 }}
                class="fixed inset-0 z-50 bg-black/50"
            ></div>
            <div
                transition:fly={{ x: 320, duration: 220 }}
                class="fixed inset-y-0 right-0 z-60 flex w-[min(85vw,22rem)] flex-col bg-white pb-[env(safe-area-inset-bottom)] shadow-lg"
            >
                <Dialog.Content class="flex min-h-0 flex-1 flex-col">
                    <div class="flex items-center justify-between border-b p-4">
                        <Dialog.Title
                            class="font-archivo flex items-center gap-2 text-base font-semibold text-gray-900"
                        >
                            <Users size={18} class="text-se" />
                            Officials
                        </Dialog.Title>
                        <Dialog.Close
                            class="cursor-pointer rounded-full p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-se"
                            aria-label="Close"
                        >
                            <X size={18} />
                        </Dialog.Close>
                    </div>

                    <div class="min-h-0 flex-1 overflow-y-auto p-4">
                        {#if !presence.connected}
                            <p class="text-sm text-gray-500">Connecting…</p>
                        {:else}
                            <SectionLabel class="mb-2"
                                >Online · {presence.count}</SectionLabel
                            >
                            <div
                                class="divide-y overflow-hidden rounded-lg border"
                            >
                                {#each presence.sorted as member (member.id)}
                                    {@const isMe = member.id === presence.me?.id}
                                    <div animate:flip={{ duration: 200 }}>
                                        {#if isMe}
                                            <div class="{rowClass} bg-se/5">
                                                {@render row(member, true)}
                                            </div>
                                        {:else}
                                            <button
                                                type="button"
                                                class="{rowClass} cursor-pointer bg-white transition-colors hover:bg-gray-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-se focus-visible:ring-inset"
                                                onclick={() => poke(member)}
                                            >
                                                {@render row(member, false)}
                                            </button>
                                        {/if}
                                    </div>
                                {/each}
                            </div>

                            {#if presence.count > 1}
                                <p class="mt-3 text-center text-xs text-gray-400">
                                    Tap someone to say hi 👋
                                </p>
                            {:else}
                                <p class="mt-3 text-center text-xs text-gray-400">
                                    No one else is online yet
                                </p>
                            {/if}

                            {#if offline.length}
                                <SectionLabel class="mt-6 mb-2"
                                    >Offline · {offline.length}</SectionLabel
                                >
                                <div
                                    class="divide-y overflow-hidden rounded-lg border"
                                >
                                    {#each offline as official (official.id)}
                                        <div
                                            class="{rowClass} bg-white"
                                            animate:flip={{ duration: 200 }}
                                        >
                                            <Avatar
                                                name={official.name}
                                                photo={official.photo}
                                                dimmed
                                            />
                                            <span
                                                class="flex min-w-0 flex-1 flex-col"
                                            >
                                                <span
                                                    class="truncate text-sm font-medium text-gray-500"
                                                    >{official.name}</span
                                                >
                                                <span class="text-xs text-gray-400"
                                                    >{official.role}</span
                                                >
                                            </span>
                                        </div>
                                    {/each}
                                </div>
                            {/if}
                        {/if}
                    </div>
                </Dialog.Content>
            </div>
        {/if}
    </Dialog.Portal>
</Dialog.Root>
