<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    public function __construct(public string $title = 'AMIS Admin') {}

    public function render()
    {
        return view('layouts.app');
    }
}
