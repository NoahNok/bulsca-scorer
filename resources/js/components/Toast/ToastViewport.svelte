<script lang="ts">
    import { toastState } from "@/lib/toast.svelte";

    import ToastItem from "./ToastItem.svelte";
</script>

<div
    class="pointer-events-none fixed right-4 top-4 z-50 flex w-[min(92vw,24rem)] flex-col gap-2"
>
    {#each toastState as toast (toast.id)}
        <ToastItem {toast} />
    {/each}
</div>
