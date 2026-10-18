<script lang="ts">
    import type { ConfirmJudge, ConfirmSercEntity } from "@/types/results";
    import { MessageSquareText } from "@lucide/svelte";
    import ViolationList from "./ViolationList.svelte";

    let {
        row,
        judges,
        useTanks = false,
    }: {
        row: ConfirmSercEntity;
        judges: ConfirmJudge[];
        useTanks?: boolean;
    } = $props();

    const formatScore = (n: number) => +n.toFixed(2);
</script>

<article class="overflow-hidden rounded-xl border bg-white shadow-sm">
    <header class="flex items-center gap-3 p-3">
        <span
            class="flex h-10 min-w-10 shrink-0 flex-col items-center justify-center rounded-lg bg-gray-100 px-1.5 text-gray-600"
        >
            <span class="text-sm leading-none font-semibold">{row.draw}</span>
            {#if useTanks && row.tank}
                <span class="text-[10px] leading-tight">T{row.tank}</span>
            {/if}
        </span>

        <div class="min-w-0 flex-1">
            <p class="truncate text-sm font-semibold text-gray-900">
                {row.entity?.name ?? "No entity"}
            </p>
            {#if row.missing > 0}
                <p class="text-xs font-medium text-amber-700">
                    {row.missing} mark{row.missing === 1 ? "" : "s"} missing
                </p>
            {/if}
        </div>

        <div class="shrink-0 text-right">
            <p class="text-xs text-gray-500">Total</p>
            <p class="font-archivo text-lg leading-none font-semibold">
                {formatScore(row.total)}
            </p>
        </div>
    </header>

    {#if row.dqs.length || row.penalties.length || row.pending.length}
        <ViolationList violations={row} class="border-t px-3 py-2" />
    {/if}

    {#each judges as judge (judge.id)}
        <section class="border-t">
            <div class="flex items-center justify-between bg-gray-50 px-3 py-1.5">
                <h4 class="truncate text-xs! text-gray-600">{judge.name}</h4>
                <span class="text-xs font-semibold text-gray-700">
                    {formatScore(row.judgeTotals[judge.id] ?? 0)}
                </span>
            </div>

            <dl class="divide-y">
                {#each judge.markingPoints as mp (mp.id)}
                    {@const mark = row.marks[mp.id]}
                    <div class="flex items-center gap-3 px-3 py-1.5 text-sm">
                        <dt class="min-w-0 flex-1 text-gray-700">
                            {mp.name}
                            {#if mp.weight !== 1}
                                <span class="text-xs text-gray-400">×{mp.weight}</span>
                            {/if}
                        </dt>
                        <dd
                            class="w-10 shrink-0 text-right font-mono font-semibold {mark ===
                                null || mark === undefined
                                ? 'text-amber-700'
                                : 'text-gray-900'}"
                        >
                            {mark ?? "–"}
                        </dd>
                    </div>
                {/each}
            </dl>
        </section>
    {/each}

    {#if row.notes.length > 0}
        <div class="flex flex-col gap-2 border-t bg-gray-50 p-3">
            {#each row.notes as note, i (i)}
                <div class="flex items-start gap-2 text-sm text-gray-700">
                    <MessageSquareText size={14} class="mt-0.5 shrink-0 text-gray-400" />
                    <p class="min-w-0">
                        {#if note.judge}
                            <span class="font-semibold">{note.judge}:</span>
                        {/if}
                        {note.note}
                    </p>
                </div>
            {/each}
        </div>
    {/if}
</article>
