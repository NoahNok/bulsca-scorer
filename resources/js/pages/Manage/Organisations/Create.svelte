<!--
    New organisation: a name and an optional logo (picked or dropped, previewed round as it shows on the
    site). You become its first Organiser, and it lands on the organisation's page.
-->
<script lang="ts">
    import { store } from "@/actions/App/Http/Controllers/Manage/OrganisationCreateController";
    import Field from "@/manage/components/Field.svelte";
    import FormPage from "@/manage/components/FormPage.svelte";
    import SubmitButton from "@/manage/components/SubmitButton.svelte";
    import { home } from "@/routes/manage";
    import { Form, Link } from "@inertiajs/svelte";
    import { ImagePlus } from "@lucide/svelte";

    const accepted = ["image/png", "image/jpeg", "image/webp"];

    let input: HTMLInputElement;
    let preview = $state<string | null>(null);
    let dragging = $state(false);
    let wrongType = $state(false);

    function show(file: File | undefined) {
        if (preview) URL.revokeObjectURL(preview);
        preview = file ? URL.createObjectURL(file) : null;
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

<svelte:head>
    <title>New organisation - Scoring.Events</title>
</svelte:head>

<FormPage title="New organisation" lead="You'll be its first Organiser, and can invite others from its page.">
    <Form action={store()}>
        {#snippet children({ errors, processing })}
            <Field id="name" name="name" label="Name" required maxlength={255} error={errors.name} />

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
                        {#if preview}
                            <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm" onclick={remove}>Remove</button>
                        {:else}
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
                        aria-describedby="logo-hint{errors.logo || wrongType ? ' logo-error' : ''}"
                        onchange={() => {
                            wrongType = false;
                            show(input.files?.[0]);
                        }}
                    />
                </div>
                <span id="logo-hint" class="v2-hint">PNG, JPG or WebP, up to 10 MB. Square logos fit the circle best.</span>
                {#if errors.logo || wrongType}
                    <span id="logo-error" class="v2-error">{wrongType ? "Choose a PNG, JPG or WebP image." : errors.logo}</span>
                {/if}
            </div>

            <div class="p-actions">
                <SubmitButton {processing}>Create organisation</SubmitButton>
                <Link href={home()} class="v2-link-muted">Cancel</Link>
            </div>
        {/snippet}
    </Form>
</FormPage>
