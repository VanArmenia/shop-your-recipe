<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Reviews extends Component
{
    public $handler;
    /**
     * Create a new component instance.
     * @param array $reviews
     * @param string $handler
     */
    public function __construct($handler)
    {
        $this->handler = $handler;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.reviews');
    }
}
