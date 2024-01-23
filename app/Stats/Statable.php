<?php

namespace App\Stats;

use Illuminate\Support\Facades\Cache;

abstract class Statable
{

    private string $name, $view;
    private $data;
    public array $baseOptions = [];

    public function __construct(string $name, string $view = "")
    {
        $this->name = $name;
        $this->view = $view === "" ? $name : $view;
    }

    /**
     * Implement data collection method
     * $options might contain things like 'team', 'club' etc
     * Data is automatically cached by the class forever
     */
    public abstract function compute(array $options);

    public function computeFor(array $options)
    {

        $options = array_merge($this->baseOptions, $options);

        $this->data = Cache::rememberForever('stats-statable-cache:' . $this->name . ':' . implode('.', $options), function () use ($options) {
            return $this->compute($options);
        });
    }

    public function render()
    {
        return view('public-results.stats.templates.' . $this->view, ['data' => $this->data]);
    }
}
