<div class="card card-row md:space-x-4 md:items-center">


    <div class="md:text-center text-sm text-gray-500 md:px-2 flex md:flex-col space-x-2 md:space-x-0">
        <span>{{ $time->format('h:ia') }}</span>
        <span>{{ $time->format('d/m/y') }}</span>
    </div>

    <div class="md:px-4 ">
        <h5 class="hmb-0">{{ $title }}</h5>
        <p class="text-gray-500 text-sm -mt-1">Action type: <span class="capitalize">{{ $action }}</span> &nbsp;
            Who:
            {{ $judge }}</p>
        <p>{{ $description }}</p>
    </div>
</div>
