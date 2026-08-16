<script lang="ts">
    import GenericDialog, {
        type DialogControls,
    } from "@/components/GenericDialog.svelte";
    import Button from "@/components/Button.svelte";

    let {
        open = $bindable(false),
        title,
        description,
        confirmLabel = "Confirm",
        cancelLabel = "Cancel",
        confirmVariant = "danger",
        triggerLabel = "Open",
        triggerVariant = "primary",
        triggerClass = "",
        triggerType = "button",
        triggerIcon,
        onConfirm,
        onCancel,
    }: {
        open?: boolean;
        title: string;
        description: string;
        confirmLabel?: string;
        cancelLabel?: string;
        confirmVariant?: "primary" | "secondary" | "success" | "danger";
        triggerLabel?: string;
        triggerVariant?: "primary" | "secondary" | "success" | "danger";
        triggerClass?: string;
        triggerType?: "button" | "submit" | "reset";
        triggerIcon?: any;
        onConfirm?: () => void;
        onCancel?: () => void;
    } = $props();

    const handleConfirm = (dialog: DialogControls) => {
        onConfirm?.();
        dialog.close();
    };
</script>

<GenericDialog
    {title}
    {cancelLabel}
    {triggerLabel}
    {triggerVariant}
    {triggerClass}
    {triggerType}
    {triggerIcon}
>
    <p>{description}</p>
    {#snippet footer(dialog)}
        <Button
            variant={confirmVariant}
            label={confirmLabel}
            type="button"
            onclick={() => handleConfirm(dialog)}
        />
    {/snippet}
</GenericDialog>
