<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SEModal extends Component
{

    private string $id, $title;

    private bool $open = false;

    private $footer = null;

    /**
     * Create a new component instance.
     */
    public function __construct($id, $title, $open = false, $footer = null)
    {
        $this->id = $id;
        $this->title = $title;
        $this->open = $open;
        $this->footer = $footer;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.s-e-modal', [
            'id' => $this->id,
            'title' => $this->title,
            'open' => $this->open,
            'footer' => $this->footer,
        ]);
    }
}
