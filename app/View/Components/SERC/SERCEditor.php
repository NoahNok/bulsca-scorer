<?php

namespace App\View\Components\SERC;

use App\Models\SERC;
use App\Models\SERC\MarkingPointTemplate;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SERCEditor extends Component
{

    private ?SERC $serc;
    private bool $edit;

    /**
     * Create a new component instance.
     */
    public function __construct(?SERC $serc = null, bool $edit = false)
    {
        $this->serc = $serc;
        $this->edit = $edit;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        $templates = MarkingPointTemplate::select('id', 'name')->orderBy('default', 'desc')->get();

        return view('components.serc.serc-editor', ['templates' => $templates, 'configuration' => $this->serc?->getSERCConfiguration() ?? [], 'serc' => $this->serc, 'edit' => $this->edit, 'comp' => request()->route('comp')]);
    }
}
