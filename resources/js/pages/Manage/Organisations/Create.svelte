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
                <label for="logo">Logo <span class="o-optional">(optional)</span></label>
                <div
                    class={["o-logo", dragging && "is-dragging"]}
                    role="group"
                    aria-label="Logo"
                    ondragover={(event) => {
                        event.preventDefault();
                        dragging = true;
                    }}
                    ondragleave={() => (dragging = false)}
                    ondrop={onDrop}
                >
                    <span class="o-preview">
                        {#if preview}
                            <img src={preview} alt="" />
                        {:else}
                            <ImagePlus size={22} aria-hidden="true" />
                        {/if}
                    </span>
                    <div class="o-pick">
                        <button type="button" class="v2-btn v2-btn-sm" onclick={() => input.click()}>
                            {preview ? "Change logo" : "Choose a logo"}
                        </button>
                        {#if preview}
                            <button type="button" class="v2-btn v2-btn-ghost v2-btn-sm" onclick={remove}>Remove</button>
                        {:else}
                            <span class="o-drop">or drop one here</span>
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

<style>
    .o-optional {
        font-weight: 400;
        color: var(--v2-muted);
    }

    .o-logo {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        border: 1px dashed var(--v2-border);
        border-radius: 0.75rem;
        transition:
            border-color 150ms,
            background-color 150ms;
    }

    .o-logo.is-dragging {
        border-color: var(--v2-teal);
        background: var(--v2-teal-soft);
    }

    .o-preview {
        display: grid;
        flex-shrink: 0;
        place-items: center;
        width: 4rem;
        height: 4rem;
        overflow: hidden;
        border-radius: 9999px;
        background: var(--v2-sunk);
        color: var(--v2-muted);
    }

    .o-preview img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .o-pick {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
    }

    .o-drop {
        font-size: 0.875rem;
        color: var(--v2-muted);
    }

    @media (prefers-reduced-motion: reduce) {
        .o-logo {
            transition: none;
        }
    }
</style>
