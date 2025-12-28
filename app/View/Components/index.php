<?php

namespace App\View\Components;

use Illuminate\View\Component;

class index extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $user_privilege_status_type;

    public $user_privilege_title;

    public $user_privilege_type;

    public $page_layout;

    public $print_header;

    public function __construct($user_privilege_status_type, $user_privilege_title, $user_privilege_type, $page_layout, $print_header)
    {
        $this->user_privilege_status_type = $user_privilege_status_type;
        $this->user_privilege_title = $user_privilege_title;
        $this->user_privilege_type = $user_privilege_type;
        $this->page_layout = $page_layout;
        $this->print_header = $print_header;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.index');
    }
}
