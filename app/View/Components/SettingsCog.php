<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SettingsCog extends Component
{

    private String $href;

    /**
     * Create a new component instance.
     */
    public function __construct(String $href = "#")
    {
        $this->href = $href;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.settings-cog', ['href' => $this->href]);
    }
}
