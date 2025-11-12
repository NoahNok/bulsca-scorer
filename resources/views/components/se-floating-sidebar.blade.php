<div class="grid-3 3xl:!grid-cols-4 " {{ $attributes->merge() }}>
    <div class="flex flex-col space-y-4 col-span-2 xl:!col-span-3">

        {{ $slot }}

    </div>

    <div class="flex flex-col space-y-4">

        <div class="sticky top-4">



            @isset($sidebar)
                {{ $sidebar }}
            @endisset






        </div>


    </div>
</div>
