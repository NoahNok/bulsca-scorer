<?php

namespace App\View\Components;

use App\Models\AbstractClasses\Loggable;
use Illuminate\View\Component;

class LoggableItem extends Component
{

    private $loggable;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($loggable)
    {
        $this->loggable = $loggable;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {


        $title = $this->replaceVariables($this->loggable->loggable()->getJudgeLogTitle());
        $description = $this->replaceVariables($this->loggable->loggable()->getJudgeLogDescription());
        $time = $this->loggable->created_at;
        $action = $this->loggable->action;
        $judge = $this->loggable->judge_name;


        return view('components.loggable-item', compact('title', 'description', 'time', 'action', 'judge'));
    }

    private function replaceVariables($string)
    {

        return str_replace(
            [
                '{team}',
                '{competition}',
                '{judge}',
                '{event}'
            ],
            [
                $this->loggable->loggable()->resolveJudgeLogTeam()->getFullname(),
                $this->loggable->competition,
                $this->loggable->judge_name,
                $this->loggable->loggable()->resolveJudgeLogName()
            ],
            $string
        );
    }
}
