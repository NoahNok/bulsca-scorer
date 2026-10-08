<script lang="ts">
    import { cn } from "@/utils/utils";
    import { Check } from "@lucide/svelte";

    let {
        id = "sign-off",
        name,
        class: className = "",
    }: {
        id?: string;
        name?: string;
        class?: string;
    } = $props();
</script>

<label
    for={id}
    class={cn(
        "flex cursor-pointer items-start gap-3 rounded-xl border bg-gray-50 p-4",
        className,
    )}
>
    <span class="relative mt-0.5 flex size-5 shrink-0">
        <input
            type="checkbox"
            required
            {id}
            {name}
            class="peer size-5 cursor-pointer appearance-none rounded border border-gray-300 bg-white transition-colors checked:border-se checked:bg-se focus:outline-none focus-visible:ring-2 focus-visible:ring-se/40"
        />
        <Check
            size={14}
            strokeWidth={3}
            class="pointer-events-none absolute inset-0 m-auto hidden text-white peer-checked:block"
        />
    </span>
    <span class="text-sm text-gray-700">
        I acknowledge that the above results are correct and cannot be changed,
        and submission of this form acts as signing it digitally.
    </span>
</label>
