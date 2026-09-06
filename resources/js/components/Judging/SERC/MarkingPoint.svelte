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
</script>

<div
    class="flex flex-col space-y-2 border-b pb-4 {isInvalid
        ? 'outline-2 outline-offset-4 outline-red-500 rounded-md'
        : ''}"
>
    <div class="flex justify-between items-center">
        <p>{marking_point.description}</p>

        {#if settings.mode == "default" && settings.min <= 0 && settings.max >= 0}
            <input
                type="radio"
                required
                class="w-0 h-0 peer"
                value={0}
                id="mp-{marking_point.id}-0"
                name="mp-{marking_point.id}"
                bind:group={value}
            />
            <label
                for="mp-{marking_point.id}-0"
                class="  flex items-center justify-center px-4 py-0.5 font-semibold rounded-xs bg-gray-200 text-xs peer-checked:bg-bulsca_red peer-checked:text-white"
            >
                ZERO
            </label>
        {/if}
    </div>
    {#if settings.mode === "default"}
        <div class="grid grid-cols-5 gap-2 gap-y-4">
            {#each markOptions as markOption}
                <div class="flex items-center justify-center">
                    <input
                        type="radio"
                        required
                        class="w-0 h-0 peer"
                        value={markOption}
                        name="mp-{marking_point.id}"
                        bind:group={value}
                        id="mp-{marking_point.id}-{markOption}"
                    />
                    <label
                        for="mp-{marking_point.id}-{markOption}"
                        class="w-6 h-6 flex items-center justify-center p-4 font-semibold font-mono rounded-md bg-gray-200 text-sm peer-checked:bg-bulsca peer-checked:text-white"
                    >
                        {markOption}
                    </label>
                </div>
            {/each}
        </div>

        {#if settings.use_toggle_for_half}
            <div class="flex items-center justify-center mt-2">
                <button
                    type="button"
                    class="badge font-mono! text-black! {half_open
                        ? 'bg-bulsca! text-white!'
                        : 'bg-gray-200!'}"
                    onclick={() => (half_open = !half_open)}
                >
                    Toggle Half Marks</button
                >
            </div>
        {/if}

        {#if marking_point.stats}
            <div class="text-gray-500 pt-2 flex justify-between">
                <small>Min: {marking_point.stats.min}</small><small
                    >Avg: {marking_point.stats.avg}</small
                ><small
                    >Max:
                    {marking_point.stats.max}</small
                >
            </div>
        {/if}
    {/if}

    {#if settings.mode === "choice"}
        <div class="grid gap-2 gap-y-4 {choiceColsClass}">
            {#each settings.choice as choice, index}
                <div class="flex items-center justify-center">
                    <div class="flex items-center justify-center">
                        <input
                            type="radio"
                            required
                            class="w-0 h-0 peer"
                            value={choice.value}
                            name="mp-{marking_point.id}"
                            bind:group={value}
                            id="mp-{marking_point.id}-choice-{index}"
                        />
                        <label
                            for="mp-{marking_point.id}-choice-{index}"
                            class=" h-6 flex items-center justify-center p-4 font-semibold font-mono rounded-md bg-gray-200 text-sm peer-checked:bg-bulsca peer-checked:text-white"
                        >
                            {choice.label}
                        </label>
                    </div>
                </div>
            {/each}
        </div>
    {/if}
</div>
