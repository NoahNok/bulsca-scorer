<!--
    A user's sign-in QR code, in a v2 modal (a native <dialog>), on their Admin page. It opens whenever a new
    `qr` arrives. Scanning it signs that phone in to Digital Judge as them, once. While it's open it checks
    every couple of seconds whether the code's been used, and if so asks for a new one (`onrenew`) straight
    away, so a queue of officials can scan one after another. A countdown shows how long it's valid for; once
    it runs out, New code asks for another.
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
        qr: { svg: string; /** ISO 8601 */ expiresAt: string; /** Url answering { used } */ status: string } | null;
        name: string;
        renewing?: boolean;
        onrenew: () => void;
    } = $props();

    const POLL_MS = 2000;

    let dialog: HTMLDialogElement;
    let open = $state(false);
    let now = $state(Date.now());
    const timer = setInterval(() => (now = Date.now()), 1000);
    onDestroy(() => clearInterval(timer));

    /** How many phones have signed in with codes from this dialog */
    let signedIn = $state(0);
    /** Another tab or Admin made a newer code for them, so this one's dead */
    let replaced = $state(false);

    $effect(() => {
        if (!qr) return;
        replaced = false;
        if (!dialog.open) {
            signedIn = 0;
            now = Date.now();
            dialog.showModal();
            open = true;
        }
    });

    const left = $derived(qr ? Math.max(0, Math.ceil((Date.parse(qr.expiresAt) - now) / 1000)) : 0);
    const expired = $derived(left === 0);
    const countdown = $derived(`${Math.floor(left / 60)}:${String(left % 60).padStart(2, "0")}`);

    // Watch the code on show until it's used, expires, is replaced or the dialog closes
    $effect(() => {
        if (!qr || !open || expired || replaced || renewing) return;
        const url = qr.status;
        let stopped = false;

        const poll = setInterval(async () => {
            try {
                const response = await fetch(url, { headers: { Accept: "application/json" } });
                if (stopped) return;
                if (response.status === 404) {
                    replaced = true;
                } else if (response.ok && (await response.json()).used && !stopped) {
                    stopped = true;
                    signedIn++;
                    onrenew();
                }
            } catch {
                // offline for a moment: try again next time
            }
        }, POLL_MS);

        return () => {
            stopped = true;
            clearInterval(poll);
        };
    });
</script>

<dialog
    bind:this={dialog}
    class="v2-modal"
    aria-labelledby="login-qr-title"
    onclose={() => (open = false)}
    onclick={(event) => event.target === dialog && dialog.close()}
>
    <h2 id="login-qr-title">Sign in as {name}</h2>
    <p>Scan this with the official's phone camera to sign it in to Digital Judge. Each code works once, and a new one appears after it's used.</p>

    {#if qr}
        <div class={["mx-auto mt-4 w-fit rounded-md bg-[#fff] p-2 [&_svg]:size-64", (expired || replaced || renewing) && "opacity-20"]} aria-hidden="true">
            {@html qr.svg}
        </div>
        <p class="mt-3 text-center text-sm font-semibold tabular-nums" role="status">
            {#if renewing}
                Making a new code…
            {:else if replaced}
                <span class="text-v2-danger-text">Replaced by a newer code</span>
            {:else if expired}
                <span class="text-v2-danger-text">Expired</span>
            {:else}
                Expires in {countdown}
            {/if}
        </p>
        {#if signedIn > 0}
            <p class="mt-1 text-center text-sm">
                <span class="text-v2-success-text">{signedIn === 1 ? "1 phone" : `${signedIn} phones`} signed in</span>
            </p>
        {/if}
    {/if}

    <div class="v2-modal-actions">
        <button type="button" class="v2-btn" onclick={() => dialog.close()}>Close</button>
        <button type="button" class={["v2-btn", (expired || replaced) && "v2-btn-primary"]} disabled={renewing} onclick={onrenew}>New code</button>
    </div>
</dialog>
