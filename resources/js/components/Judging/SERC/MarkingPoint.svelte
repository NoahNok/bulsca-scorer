<script lang="ts">
    import type { MarkingPoint } from "@/types/base";

    let {
        marking_point,
        hasSubmitted = $bindable<boolean>(),
        value = $bindable<number | null>(),
    }: {
        marking_point: MarkingPoint;
        hasSubmitted: boolean;
        value?: number | null;
    } = $props();

    let half_open = $state(false);

    const settings = $derived.by(() => {
        if (!marking_point.template) {
            throw new Error("Marking point template required, but not given");
        }

        return marking_point.template;
    });

    $effect(() => {
        if (value) {
            // if value is half, then set half_open to true
            if (value % 1 !== 0) {
                half_open = true;
            }
        }
    });

    const markOptions = $derived.by(() => {
        if (!marking_point.template) {
            throw new Error("Marking point template required, but not given");
        }

        const options: number[] = [];

        for (let i = settings.min; i <= settings.max; i += settings.step) {
            let isHalfValue = i % 1 !== 0;

            // show half values on or whole values only depending on half_open state
            if (isHalfValue && !half_open) {
                continue;
            }

            if (!isHalfValue && half_open) {
                continue;
            }

            if (i === 0) {
                continue;
            }
            options.push(i);
        }

        return options;
    });

    const choiceColsClass = $derived.by(() => {
        switch (settings.choice.length) {
            case 1:
                return "grid-cols-1 sm:grid-cols-1";
            case 2:
                return "grid-cols-2 sm:grid-cols-2";
            case 3:
                return "grid-cols-2 sm:grid-cols-3";
            case 4:
                return "grid-cols-2 sm:grid-cols-4";
            default:
                return "grid-cols-2 sm:grid-cols-5";
        }
    });

    const isInvalid = $derived.by(() => {
        if (!hasSubmitted) {
            return false;
        }

        return value === null || value === undefined;
    });

    const optionClass =
        "flex h-10 w-full cursor-pointer items-center justify-center rounded-lg border bg-white font-mono text-sm font-semibold text-gray-700 transition-colors hover:border-gray-400 peer-checked:border-se peer-checked:bg-se peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-se/40";
</script>

<div
    class="flex flex-col gap-2 py-3 {isInvalid
        ? '-mx-2 rounded-lg bg-red-50 px-2 outline-2 outline-red-400'
        : ''}"
>
    <div class="relative flex items-center justify-between gap-3">
        <p class="text-sm font-medium text-gray-900">
            {marking_point.description}
        </p>

        {#if settings.mode == "default" && settings.min <= 0 && settings.max >= 0}
            <input
                type="radio"
                required
                class="peer sr-only"
                value={0}
                id="mp-{marking_point.id}-0"
                name="mp-{marking_point.id}"
                bind:group={value}
            />
            <label
                for="mp-{marking_point.id}-0"
                class="shrink-0 cursor-pointer rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600 transition-colors peer-checked:bg-red-600 peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-red-300"
            >
                ZERO
            </label>
        {/if}
    </div>

    {#if settings.mode === "default"}
        <div class="grid grid-cols-5 gap-x-2 gap-y-1.5">
            {#each markOptions as markOption}
                <div class="relative">
                    <input
                        type="radio"
                        required
                        class="peer sr-only"
                        value={markOption}
                        name="mp-{marking_point.id}"
                        bind:group={value}
                        id="mp-{marking_point.id}-{markOption}"
                    />
                    <label
                        for="mp-{marking_point.id}-{markOption}"
                        class={optionClass}
                    >
                        {markOption}
                    </label>
                </div>
            {/each}
        </div>

        {#if settings.use_toggle_for_half}
            <div class="flex justify-center">
                <button
                    type="button"
                    aria-pressed={half_open}
                    class="cursor-pointer rounded-full px-3 py-1 font-mono text-xs font-semibold transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-se {half_open
                        ? 'bg-black text-white'
                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200'}"
                    onclick={() => (half_open = !half_open)}
                >
                    Half marks
                </button>
            </div>
        {/if}

        {#if marking_point.stats}
            <div class="flex justify-between text-xs text-gray-400">
                <span>Min: {marking_point.stats.min}</span>
                <span>Avg: {marking_point.stats.avg}</span>
                <span>Max: {marking_point.stats.max}</span>
            </div>
        {/if}
    {/if}

    {#if settings.mode === "choice"}
        <div class="grid gap-x-2 gap-y-1.5 {choiceColsClass}">
            {#each settings.choice as choice, index}
                <div class="relative">
                    <input
                        type="radio"
                        required
                        class="peer sr-only"
                        value={choice.value}
                        name="mp-{marking_point.id}"
                        bind:group={value}
                        id="mp-{marking_point.id}-choice-{index}"
                    />
                    <label
                        for="mp-{marking_point.id}-choice-{index}"
                        class="{optionClass} px-3"
                    >
                        {choice.label}
                    </label>
                </div>
            {/each}
        </div>
    {/if}
</div>
