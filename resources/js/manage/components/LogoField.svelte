<!--
    A form's `logo` file field: pick one or drop it on the box, previewed round as it shows on the site.
    PNG, JPEG or WebP only; a dropped file of another type is turned away with an error. With `current`
    (the logo already saved) the preview starts from it, and putting the new pick back returns to it.
    `onpick` says whether a new file is chosen, for an unsaved changes guard.
-->
<script lang="ts">
    import { ImagePlus } from "@lucide/svelte";

    let {
        error,
        current = null,
        onpick,
    }: { error?: string; current?: string | null; onpick?: (picked: boolean) => void } = $props();

    const accepted = ["image/png", "image/jpeg", "image/webp"];

    let input: HTMLInputElement;
    let picked = $state<string | null>(null);
    let dragging = $state(false);
    let wrongType = $state(false);

    const preview = $derived(picked ?? current);

    function show(file: File | undefined) {
        if (picked) URL.revokeObjectURL(picked);
        picked = file ? URL.createObjectURL(file) : null;
        onpick?.(picked !== null);
    }

    function onDrop(event: DragEvent) {
        event.preventDefault();
        dragging = false;
        const file = event.dataTransfer?.files[0];
        if (!file) return;

        wrongType = !accepted.includes(file.type);
        if (wrongType) return;

        // Put it in the file input, so the form sends it like a picked one
        const files = new DataTransfer();
        files.items.add(file);
        input.files = files.files;
        show(file);
    }

    function remove() {
        input.value = "";
        show(undefined);
    }
</script>

<div class="v2-field">
    <label for="logo">Logo <span class="font-normal text-v2-muted">(optional)</span></label>
    <div
        class={[
            "flex items-center gap-4 rounded-xl border border-dashed p-3 transition-colors motion-reduce:transition-none",
            dragging ? "border-v2-teal bg-v2-teal-soft" : "border-v2-border",
        ]}
        role="group"
        aria-label="Logo"
        ondragover={(event) => {
            event.preventDefault();
            dragging = true;
        }}
        ondragleave={() => (dragging = false)}
        ondrop={onDrop}
    >
        <span class="grid size-16 shrink-0 place-items-center overflow-hidden rounded-full bg-v2-sunk text-v2-muted">
            {#if preview}
                <img src={preview} alt="" class="size-full object-contain" />
            {:else}
                <ImagePlus size={22} aria-hidden="true" />
            {/if}
        </span>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" class="v2-btn v2-btn-sm" onclick={() => input.click()}>
                {preview ? "Change logo" : "Choose a logo"}
            </button>
            {#if picked}
                <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm" onclick={remove}>{current ? "Keep the current one" : "Remove"}</button>
            {:else if !current}
                <span class="text-sm text-v2-muted">or drop one here</span>
            {/if}
        </div>
        <input
            bind:this={input}
            id="logo"
            name="logo"
            type="file"
            accept={accepted.join(",")}
            class="sr-only"
            tabindex="-1"
            aria-describedby="logo-hint{error || wrongType ? ' logo-error' : ''}"
            onchange={() => {
                wrongType = false;
                show(input.files?.[0]);
            }}
        />
    </div>
    <span id="logo-hint" class="v2-hint">PNG, JPG or WebP, up to 10 MB. Square logos fit the circle best.</span>
    {#if error || wrongType}
        <span id="logo-error" class="v2-error">{wrongType ? "Choose a PNG, JPG or WebP image." : error}</span>
    {/if}
</div>
