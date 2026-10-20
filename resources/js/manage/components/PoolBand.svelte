<!--
    The signed-out pages' brand band (AuthLayout): one lane of a pool on the favicon's tile, its number
    painted big behind it, and a big line under it. The mouse swims it: up and down the window picks lane
    1 to 8, left to right takes the swimmer from one wall to the other, leaving bubbles and ripples behind.
    It treads water when the mouse stops, now and then dives and swims a few strokes underwater, and stays
    under, looking away, while a password field has focus. Dark in both themes. Desktop only: beside the form, and left out on phones.
-->
<script lang="ts">
    import BrandMark from "@/manage/components/BrandMark.svelte";
    import { page } from "@inertiajs/svelte";
    import { prefersReducedMotion } from "svelte/motion";
    import { fade } from "svelte/transition";

    let { headline }: { headline: string } = $props();

    const lanes = 8;

    // lane 4 at the start wall until the mouse moves
    let lane = $state(4);
    let pool: HTMLDivElement;
    let swimmer: HTMLSpanElement;
    let canvas: HTMLCanvasElement;

    // How far along the lane the swimmer is, and where the mouse wants it. It eases there a frame at a time
    // and is written straight to --progress, so the browser only moves layers and Svelte doesn't re-render
    let progress = 0;
    let target = 0;
    let frame = 0;
    let last = 0;

    // The water it leaves behind: bubbles off its feet and a ripple each time an arm goes in
    type Splash = {
        x: number;
        y: number;
        vx: number;
        vy: number;
        r: number;
        grow: number;
        age: number;
        life: number;
        ring: boolean;
    };
    const splashes: Splash[] = [];
    let backwards = false;
    let sinceStroke = 0;

    // Treading water: a small ripple now and then while it waits
    const treadEvery = 1500;
    let treadRipple = false;

    // The easter egg: on about one stroke in 150 it dives, and comes up 2.6s later
    const diveOdds = 1 / 150;
    const diveFor = 2600;
    let under = 0;
    let plunge = false;

    // Looking away while a password is typed: under for as long as a password field (or its show/hide
    // button) has focus, then up shortly after
    function isPassword(element: EventTarget | null) {
        if (!(element instanceof HTMLElement)) {
            return false;
        }

        const field = element.matches("button[aria-controls]")
            ? document.getElementById(element.getAttribute("aria-controls") ?? "")
            : element;

        return !!field?.matches('input[type="password"], input[autocomplete$="-password"]');
    }

    function look(event: FocusEvent) {
        if (prefersReducedMotion.current) {
            return;
        }

        const away = isPassword(event.type === "focusin" ? event.target : event.relatedTarget);

        if (away && under !== Infinity) {
            plunge = !under;
            under = Infinity;
            wake();
        } else if (!away && under === Infinity) {
            under = 300;
            wake();
        }
    }

    function swim(event: PointerEvent) {
        if (event.pointerType !== "mouse" || prefersReducedMotion.current) {
            return;
        }

        lane = Math.min(lanes, Math.floor((event.clientY / window.innerHeight) * lanes) + 1);
        target = Math.min(1, Math.max(0, event.clientX / window.innerWidth));
        wake();
    }

    function wake() {
        if (!frame) {
            last = performance.now();
            frame = requestAnimationFrame(step);
        }
    }

    function step(now: number) {
        // capped, so a frame after the tab was hidden doesn't jump
        const dt = Math.min(now - last, 50);
        last = now;

        // read where the swimmer is before writing anything, so the frame doesn't force a layout
        const box = pool.getBoundingClientRect();
        const at = swimmer.getBoundingClientRect();
        const x = at.right - box.left;
        const y = at.top - box.top;

        // the same ease at any refresh rate: about 90% of the way there in 0.2s
        const before = progress;
        progress += (target - progress) * (1 - Math.exp(-dt / 90));

        if (Math.abs(target - progress) < 0.0005) {
            progress = target;
        }

        // lane lengths a second
        const speed = dt ? ((progress - before) / dt) * 1000 : 0;
        const swimming = Math.abs(speed) > 0.03;

        if (swimming) {
            backwards = speed < 0;
        }

        if (plunge) {
            plunge = false;
            ripple(x, y, 3, 20, 900);
        }

        if (under) {
            dive(x, y, dt);
        } else if (swimming) {
            splash(x, y, Math.abs(speed), dt);
        } else if (treadRipple) {
            ripple(x, y + (Math.random() - 0.5) * 6, 2, 10, 1100, 0.6);
        }

        treadRipple = false;

        pool.style.setProperty("--progress", String(progress));
        swimmer.classList.toggle("is-swimming", swimming);
        swimmer.classList.toggle("is-backwards", backwards);
        swimmer.classList.toggle("is-under", under > 0);
        draw(box.width, box.height, dt);

        frame = progress !== target || splashes.length || under ? requestAnimationFrame(step) : 0;
    }

    function ripple(x: number, y: number, r: number, grow: number, life: number, vx = 0) {
        splashes.push({ x, y, vx, vy: 0, r, grow, age: 0, life, ring: true });
    }

    function bubble(x: number, y: number, vx: number, vy: number) {
        splashes.push({
            x,
            y,
            vx,
            vy,
            r: 0.75 + Math.random() * 1.75,
            grow: 0,
            age: 0,
            life: 500 + Math.random() * 700,
            ring: false,
        });
    }

    // Underwater: no splash, just the odd breath of bubbles coming up off it, then a big ripple as it surfaces
    function dive(x: number, y: number, dt: number) {
        under = Math.max(0, under - dt);

        if (Math.random() < dt / 280) {
            for (let i = 1 + Math.floor(Math.random() * 3); i > 0; i--) {
                bubble(x + (Math.random() - 0.5) * 14, y + (Math.random() - 0.5) * 8, 0, (Math.random() - 0.5) * 10);
            }
        }

        if (!under) {
            ripple(x, y, 4, 26, 1200);

            for (let i = 0; i < 10; i++) {
                const angle = Math.random() * Math.PI * 2;
                bubble(x, y, Math.cos(angle) * 60, Math.sin(angle) * 60);
            }
        }
    }

    function splash(x: number, y: number, speed: number, dt: number) {
        const behind = backwards ? 1 : -1;

        // more bubbles the faster it goes
        for (let i = Math.random() < speed * dt * 0.08 ? 1 + Math.floor(speed) : 0; i > 0; i--) {
            bubble(
                x + behind * (18 + Math.random() * 6),
                y + (Math.random() - 0.5) * 10,
                behind * (10 + Math.random() * 30),
                (Math.random() - 0.5) * 24,
            );
        }

        // the arms take turns, half the 700ms stroke apart, either side of the head
        sinceStroke += dt;

        if (sinceStroke > 350) {
            sinceStroke = 0;

            if (Math.random() < diveOdds) {
                under = diveFor;
                ripple(x, y, 3, 20, 900);
                return;
            }

            ripple(x - behind * 10, y + (splashes.length % 2 ? -8 : 8), 2, 16, 900, behind * 12);
        }
    }

    function draw(width: number, height: number, dt: number) {
        const ctx = canvas.getContext("2d");

        if (!ctx) {
            return;
        }

        const ratio = window.devicePixelRatio || 1;

        if (canvas.width !== Math.round(width * ratio) || canvas.height !== Math.round(height * ratio)) {
            canvas.width = Math.round(width * ratio);
            canvas.height = Math.round(height * ratio);
        }

        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        ctx.clearRect(0, 0, width, height);

        for (let i = splashes.length - 1; i >= 0; i--) {
            const s = splashes[i];
            s.age += dt;

            if (s.age >= s.life) {
                splashes.splice(i, 1);
                continue;
            }

            const t = s.age / s.life;
            s.x += (s.vx * dt) / 1000;
            s.y += (s.vy * dt) / 1000;
            s.vx *= 0.96;
            s.vy *= 0.96;

            ctx.beginPath();

            if (s.ring) {
                // spreads out and fades, quick at first
                ctx.arc(s.x, s.y, s.r + s.grow * (1 - (1 - t) ** 2), 0, Math.PI * 2);
                ctx.strokeStyle = `rgb(255 255 255 / ${0.22 * (1 - t)})`;
                ctx.lineWidth = 1.25;
                ctx.stroke();
            } else {
                ctx.arc(s.x, s.y, s.r * (1 + t), 0, Math.PI * 2);
                ctx.fillStyle = `rgb(255 255 255 / ${0.4 * (1 - t)})`;
                ctx.fill();
            }
        }
    }

    $effect(() => {
        const tread = setInterval(() => {
            if (!prefersReducedMotion.current) {
                treadRipple = true;
                wake();
            }
        }, treadEvery);

        return () => {
            clearInterval(tread);
            cancelAnimationFrame(frame);
        };
    });
</script>

<svelte:window onpointermove={swim} onfocusin={look} onfocusout={look} />

<!-- A div, not an aside: app.css collapses every aside for the old sidebar -->
<div class="p-band">
    <a href={page.props.manage?.site.home} class="p-brand">
        <BrandMark size={36} live />
        Scoring.Events
    </a>

    <div class="p-pool" bind:this={pool} style:--lane={lane} style:--lanes={lanes} aria-hidden="true">
        <span class="p-number">{lane}</span>
        <canvas class="p-water" bind:this={canvas}></canvas>
        <div class="p-lane">
            <span class="p-rope"></span>
            <span class="p-rope p-rope-under"></span>
            <!-- Seen from above, heading for the far wall: legs, body, arms, then the cap -->
            <span class="p-swimmer" bind:this={swimmer}>
                <svg viewBox="0 0 48 24">
                    <line class="p-leg" x1="14" y1="10" x2="3" y2="8.5" />
                    <line class="p-leg p-leg-other" x1="14" y1="14" x2="3" y2="15.5" />
                    <ellipse cx="22" cy="12" rx="10" ry="5.5" fill="#fff" />
                    <line class="p-arm" x1="27" y1="7" x2="43" y2="7" />
                    <line class="p-arm p-arm-other" x1="27" y1="17" x2="43" y2="17" />
                    <circle cx="34" cy="12" r="4.5" fill="var(--v2-teal)" />
                </svg>
            </span>
        </div>
    </div>

    <!-- Old and new lines share one grid cell, so they crossfade in place -->
    <div class="p-headline">
        {#key headline}
            <p transition:fade={{ duration: prefersReducedMotion.current ? 0 : 250 }}>{headline}</p>
        {/key}
    </div>
</div>

<style>
    .p-band {
        position: sticky;
        top: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1.5rem;
        height: 100dvh;
        padding: 2.5rem;
        overflow: hidden;
        background: var(--v2-tile);
        color: #fff;
    }

    /* The pool fills the band between the brand and the line, wall to wall. Its lanes split its height;
       only the one being swum is drawn */
    .p-pool {
        position: relative;
        flex: 1;
        margin: 0 -2.5rem;
        --ease: cubic-bezier(0.2, 0.7, 0.2, 1);
    }

    /* the lane's number, centred on it and bleeding off the far wall */
    .p-number {
        position: absolute;
        right: -0.06em;
        top: calc((var(--lane) - 0.5) / var(--lanes) * 100%);
        transform: translateY(-50%);
        font-family: "Anton", sans-serif;
        font-size: min(24rem, 55dvh);
        line-height: 0.8;
        color: rgb(255 255 255 / 0.05);
        transition: top 400ms var(--ease);
    }

    .p-lane {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: calc(100% / var(--lanes));
        transform: translateY(calc((var(--lane) - 1) * 100%));
        transition: transform 400ms var(--ease);
    }

    /* Floats along each side of the lane, teal for the 5m by each wall */
    .p-rope {
        position: absolute;
        left: 0;
        right: 0;
        top: -0.25rem;
        height: 0.5rem;
        background: linear-gradient(90deg, var(--v2-teal) 0 10%, rgb(255 255 255 / 0.22) 10% 90%, var(--v2-teal) 90%);
        mask: repeating-linear-gradient(90deg, #000 0 1.125rem, transparent 1.125rem 1.625rem);
        animation: p-rope-in 900ms var(--ease) both;
    }

    .p-rope-under {
        top: auto;
        bottom: -0.25rem;
        animation-delay: 120ms;
    }

    /* The bubbles and ripples, over the whole pool so they stay where they were left when the lane moves */
    .p-water {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
    }

    /* The swimmer, across the lane between the walls. It's the lane's full width with the figure at its far
       end, so it moves by transform alone */
    .p-swimmer {
        position: absolute;
        top: 50%;
        left: 5%;
        width: 90%;
        height: 0;
        transform: translateX(calc((var(--progress, 0) - 1) * 100%));
        will-change: transform;
    }

    .p-swimmer svg {
        position: absolute;
        right: -1.5rem;
        top: -0.75rem;
        width: 3rem;
        height: 1.5rem;
        overflow: visible;
        filter: drop-shadow(0 0 0.375rem rgb(0 213 190 / 0.35));
        transition:
            transform 200ms ease-out,
            opacity 400ms ease-out,
            filter 400ms ease-out;
    }

    /* turns round to swim back */
    .p-swimmer:global(.is-backwards) svg {
        transform: scaleX(-1);
    }

    .p-leg,
    .p-arm {
        stroke: rgb(255 255 255 / 0.85);
        stroke-linecap: round;
        transform-box: fill-box;
    }

    /* Treading water: arms out to the sides sculling, and a slow kick */
    .p-arm {
        stroke-width: 2.5;
        transform-origin: left center;
        animation: p-scull 1.2s ease-in-out infinite alternate;
    }

    .p-arm-other {
        animation-name: p-scull-other;
    }

    @keyframes p-scull {
        from {
            transform: rotate(-80deg) scaleX(0.55);
        }

        to {
            transform: rotate(-45deg) scaleX(0.55);
        }
    }

    @keyframes p-scull-other {
        from {
            transform: rotate(80deg) scaleX(0.55);
        }

        to {
            transform: rotate(45deg) scaleX(0.55);
        }
    }

    .p-leg {
        stroke-width: 2.25;
        transform-origin: right center;
        animation: p-kick 600ms ease-in-out infinite alternate;
    }

    .p-leg-other {
        animation-direction: alternate-reverse;
    }

    @keyframes p-kick {
        from {
            transform: rotate(-9deg);
        }

        to {
            transform: rotate(9deg);
        }
    }

    /* Freestyle: each arm reaches past the head and pulls back to the hip, half a stroke apart */
    .p-swimmer:global(.is-swimming) .p-arm {
        animation: p-stroke 700ms ease-in-out infinite;
    }

    .p-swimmer:global(.is-swimming) .p-arm-other {
        animation-delay: -350ms;
    }

    @keyframes p-stroke {
        50% {
            transform: scaleX(-0.6);
        }
    }

    /* and a flutter kick */
    .p-swimmer:global(.is-swimming) .p-leg {
        animation-duration: 240ms;
    }

    /* Underwater: deeper and blurred, arms out in front and a dolphin kick, both legs together */
    .p-swimmer:global(.is-under) svg {
        opacity: 0.35;
        filter: blur(1.5px);
    }

    .p-swimmer:global(.is-under) .p-arm {
        animation: none;
    }

    .p-swimmer:global(.is-under) .p-leg {
        animation-duration: 400ms;
        animation-direction: alternate;
    }

    @keyframes p-rope-in {
        from {
            clip-path: inset(0 100% 0 0);
        }

        to {
            clip-path: inset(0);
        }
    }

    .p-brand {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 0.625rem;
        align-self: flex-start;
        font-weight: 800;
        font-size: 1.125rem;
    }

    .p-headline {
        position: relative;
        display: grid;
    }

    .p-headline p {
        grid-area: 1 / 1;
        max-width: 14ch;
        font-weight: 800;
        font-size: clamp(2rem, 4.5vw, 3.25rem);
        line-height: 1.05;
        text-transform: uppercase;
        text-wrap: balance;
    }

    /* Phones go straight to the form */
    @media (width < 64rem) {
        .p-band {
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .p-rope,
        .p-arm,
        .p-leg {
            animation: none;
        }

        .p-number,
        .p-lane,
        .p-swimmer svg {
            transition: none;
        }
    }
</style>
