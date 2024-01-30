<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class StatisticsController extends Controller
{
    public function prices(): array
    {
        return ['prices'=>'test'];
    }
}
