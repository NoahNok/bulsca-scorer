<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Loader extends Component
{

    private $size = 8;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($size = 8)
    {
        $this->size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.loader', ['size' => $this->size]);
    }
}
