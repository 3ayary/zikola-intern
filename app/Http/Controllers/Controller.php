<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected $pagination;
    public function __construct()
    {
        $this->pagination = request()->per_page ?? 10;
    }
}
