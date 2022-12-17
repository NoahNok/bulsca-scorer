<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AddCard extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($link = '#', $text = '')
    {
        $this->text = $text;
        $this->link = $link;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.add-card', ['link' => $this->link, 'text' => $this->text]);
    }
}
