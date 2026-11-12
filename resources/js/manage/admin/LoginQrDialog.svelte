<!--
    A user's sign-in QR code, in a v2 modal (a native <dialog>), on their Admin page. It opens whenever a new
    `qr` arrives. Scanning it signs that phone in to Digital Judge as them, once. A countdown shows how long
    it's valid for; once it runs out, New code asks for another (`onrenew`).
-->
<script lang="ts">
    import { onDestroy } from "svelte";

    let {
        qr,
        name,
        renewing = false,
        onrenew,
    }: {
        /** Null until one's been made. The svg is server-made, so it's trusted */
        qr: { svg: string; /** ISO 8601 */ expiresAt: string } | null;
        name: string;
        renewing?: boolean;
        onrenew: () => void;
    } = $props();

    let dialog: HTMLDialogElement;
    let now = $state(Date.now());
    const timer = setInterval(() => (now = Date.now()), 1000);
    onDestroy(() => clearInterval(timer));

    $effect(() => {
        if (qr && !dialog.open) {
            now = Date.now();
            dialog.showModal();
        }
    });

    const left = $derived(qr ? Math.max(0, Math.ceil((Date.parse(qr.expiresAt) - now) / 1000)) : 0);
    const expired = $derived(left === 0);
    const countdown = $derived(`${Math.floor(left / 60)}:${String(left % 60).padStart(2, "0")}`);
</script>

<dialog
    bind:this={dialog}
    class="v2-modal"
    aria-labelledby="login-qr-title"
    onclick={(event) => event.target === dialog && dialog.close()}
>
    <h2 id="login-qr-title">Sign in as {name}</h2>
    <p>Scan this with the official's phone camera to sign it in to Digital Judge. It works once.</p>

    {#if qr}
        <div class={["mx-auto mt-4 w-fit rounded-md bg-[#fff] p-2 [&_svg]:size-64", expired && "opacity-20"]} aria-hidden="true">
            {@html qr.svg}
        </div>
        <p class="mt-3 text-center text-sm font-semibold tabular-nums" role="status">
            {#if expired}
                <span class="text-v2-danger-text">Expired</span>
            {:else}
                Expires in {countdown}
            {/if}
        </p>
    {/if}

    <div class="v2-modal-actions">
        <button type="button" class="v2-btn" onclick={() => dialog.close()}>Close</button>
        <button type="button" class={["v2-btn", expired && "v2-btn-primary"]} disabled={renewing} onclick={onrenew}>New code</button>
    </div>
</dialog>
