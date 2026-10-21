<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ClientPartner;
use App\Models\CompanySetting;
use App\Models\Post;
use App\Models\ProcessStep;
use App\Models\Project;
use App\Models\ServiceCategory;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        return view('public.home', [
            'company' => CompanySetting::current(),
            'featuredProjects' => Project::query()
                ->published()
                ->featured()
                ->with(['industry', 'services'])
                ->orderBy('sort_order')
                ->limit(6)
                ->get(),
            'categories' => ServiceCategory::query()
                ->visible()
                ->whereHas('services', fn ($q) => $q->visible())
                ->with(['services' => fn ($q) => $q->visible()])
                ->orderBy('sort_order')
                ->get(),
            'clients' => ClientPartner::query()
                ->visible()
                ->orderBy('sort_order')
                ->get(),
            'testimonials' => Testimonial::query()
                ->published()
                ->orderBy('sort_order')
                ->limit(3)
                ->get(),
            'processSteps' => ProcessStep::visible()->get(),
            'latestPosts' => Post::query()
                ->live()
                ->with('author')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(3)
                ->get(),
        ]);
    }
}