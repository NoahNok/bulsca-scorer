<script lang="ts">
    import { cn } from "@/utils/utils";
    import { page } from "@inertiajs/svelte";

    let { class: className = "" }: { class?: string } = $props();

    const legal = $derived(page.props.legal);
</script>

<!-- The policies are Blade pages, so these are plain links (full page loads), not <Link> -->
<footer
    class={cn(
        "flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-xs text-gray-400",
        className,
    )}
>
    <a class="hover:text-gray-700 hover:underline" href={legal.terms}>Terms</a>
    <a class="hover:text-gray-700 hover:underline" href={legal.privacy}>Privacy</a>
    <a class="hover:text-gray-700 hover:underline" href={legal.cookies}>Cookies</a>
</footer>
