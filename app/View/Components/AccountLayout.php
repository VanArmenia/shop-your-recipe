<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AccountLayout extends Component
{
    public $customer;
    /**
     * Create a new component instance.
     * @param array $customer
     */

    public function __construct(?array $customer = null)
    {
        // If no customer is passed, get from auth or provide default
        $this->customer = $customer ??
            auth()->user()?->customer ??
            (object)[
                'avatar' => null,
                'first_name' => 'Guest'
            ];
    }

    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('layouts.account-layout');
    }
}
