<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;

class AboutController extends Controller
{
    public function index()
    {
        return view('public.about', ['company' => CompanySetting::current()]);
    }
}