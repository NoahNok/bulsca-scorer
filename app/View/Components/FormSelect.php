<?php

namespace App\View\Components;

use Illuminate\View\Component;

class FormSelect extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($id, $title, $type = "text", $defaultValue = "", $extraCss = "", $options = [], $deny = false)
    {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->defaultValue = $defaultValue;
        $this->extraCss = $extraCss;
        $this->options = $options;
        $this->deny = $deny;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form-select', ['id' => $this->id, 'title' => $this->title, 'type' => $this->type, 'defaultValue' => $this->defaultValue, 'css' => $this->extraCss, 'options' => $this->options, 'deny' => $this->deny]);
    }
}
