<script lang="ts">
    import { cn } from "@/utils/utils";
    import { page } from "@inertiajs/svelte";

    let { class: className = "" }: { class?: string } = $props();

    const legal = $derived(page.props.legal);
</script>

<!-- Under a form that signs someone in or gives them an account -->
<p class={cn("text-center text-xs text-gray-500", className)}>
    By continuing you agree to the
    <a class="underline hover:text-gray-900" href={legal.terms}>Terms of Service</a>
    and
    <a class="underline hover:text-gray-900" href={legal.privacy}>Privacy Policy</a>.
</p>
