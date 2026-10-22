<!--
    A small rich-text field (bold, italic, underline, links) for HTML a form sends as `name`: Quill, loaded
    from /vendor on first use as the Blade pages do. It sends an empty value when there's no text left.
-->
<script lang="ts">
    import { onMount } from "svelte";

    let {
        id,
        name,
        value = "",
        placeholder,
        labelledby,
        oninput,
    }: {
        id: string;
        name: string;
        value?: string | null;
        placeholder?: string;
        /** The id of its label: Quill's editor isn't a form control a <label for> can point at */
        labelledby?: string;
        oninput?: () => void;
    } = $props();

    const QUILL = "/vendor/quill@2.0.2/quill";

    let editor: HTMLDivElement;
    let input: HTMLInputElement;
    // svelte-ignore state_referenced_locally
    let html = $state(value ?? "");

    function load(): Promise<void> {
        if ((window as any).Quill) return Promise.resolve();

        if (!document.querySelector(`link[href="${QUILL}.snow.css"]`)) {
            document.head.append(Object.assign(document.createElement("link"), { rel: "stylesheet", href: `${QUILL}.snow.css` }));
        }

        return new Promise((resolve, reject) => {
            const script = Object.assign(document.createElement("script"), { src: `${QUILL}.js` });
            script.onload = () => resolve();
            script.onerror = reject;
            document.head.append(script);
        });
    }

    onMount(() => {
        let gone = false;

        load().then(() => {
            if (gone) return;
            const Quill = (window as any).Quill;
            const quill = new Quill(editor, {
                theme: "snow",
                placeholder,
                modules: { toolbar: [["bold", "italic", "underline", "link"]] },
            });
            quill.root.setAttribute("aria-labelledby", labelledby ?? "");
            quill.root.id = id;

            quill.on("text-change", () => {
                html = quill.getText().trim() === "" ? "" : quill.root.innerHTML;
                // A hidden input fires nothing itself, so tell the form it changed
                input.dispatchEvent(new Event("input", { bubbles: true }));
                oninput?.();
            });
        });

        return () => (gone = true);
    });
</script>

<div class="r-wrap">
    <!-- Quill reads its starting content from here -->
    <div bind:this={editor}>{@html value ?? ""}</div>
</div>
<input bind:this={input} type="hidden" {name} value={html} />

<style>
    /* Quill's snow theme, on the v2 tokens */
    .r-wrap :global(.ql-toolbar.ql-snow),
    .r-wrap :global(.ql-container.ql-snow) {
        border-color: var(--v2-border);
        font-family: inherit;
    }

    .r-wrap :global(.ql-toolbar.ql-snow) {
        border-radius: 0.5rem 0.5rem 0 0;
        background: var(--v2-sunk);
    }

    .r-wrap :global(.ql-container.ql-snow) {
        border-radius: 0 0 0.5rem 0.5rem;
        background: var(--v2-bg);
        font-size: 0.9375rem;
        min-height: 8rem;
    }

    .r-wrap :global(.ql-editor.ql-blank::before) {
        color: var(--v2-muted);
        font-style: normal;
    }

    .r-wrap :global(.ql-snow .ql-stroke) {
        stroke: var(--v2-ink);
    }

    .r-wrap :global(.ql-snow .ql-fill) {
        fill: var(--v2-ink);
    }

    .r-wrap :global(.ql-snow a) {
        color: var(--v2-teal-text);
    }
</style>
