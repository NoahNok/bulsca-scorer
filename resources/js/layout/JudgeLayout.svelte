<script lang="ts">
    import ToastViewport from "@/components/Toast/ToastViewport.svelte";
    import type { Snippet } from "svelte";

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();
</script>

<div
    class=" p-6 sm:max-w-[70%] xl:max-w-[50%] 2xl:max-w-[40%] sm:mx-auto h-screen"
>
    {@render children?.()}
</div>

<ToastViewport />
