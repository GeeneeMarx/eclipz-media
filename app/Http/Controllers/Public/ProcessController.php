<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ProcessStep;

class ProcessController extends Controller
{
    public function index()
    {
        return view('public.process', [
            'steps' => ProcessStep::visible()->get(),
        ]);
    }
}