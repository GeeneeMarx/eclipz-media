<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;

class ServicesController extends Controller
{
    public function index()
    {
        return view('public.services', [
            'categories' => ServiceCategory::query()
                ->visible()
                ->with(['services' => fn ($q) => $q->visible()])
                ->orderBy('sort_order')
                ->get()
                ->filter(fn ($category) => $category->services->isNotEmpty()),
        ]);
    }
}