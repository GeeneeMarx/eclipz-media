<?php

namespace App\Http\Controllers\Public;

use App\Enums\ProjectType;
use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class WorkController extends Controller
{
    public function index(Request $request)
    {
        // Only well-formed filter values are used. Anything else (arrays such as
        // ?industry[]=1, text where a number is expected, unknown project types) is
        // ignored instead of reaching the database query.
        $filters = Validator::make($request->query(), [
            'industry' => ['nullable', 'integer', 'min:1'],
            'service' => ['nullable', 'integer', 'min:1'],
            'type' => ['nullable', Rule::enum(ProjectType::class)],
        ])->valid();

        $projects = Project::query()
            ->published()
            ->with(['industry', 'services'])
            ->when($filters['industry'] ?? null, fn ($q, $v) => $q->where('industry_id', $v))
            ->when($filters['service'] ?? null, fn ($q, $v) => $q->whereHas('services', fn ($q2) => $q2->where('services.id', $v)))
            ->when($filters['type'] ?? null, fn ($q, $v) => $q->where('project_type', $v))
            ->orderBy('sort_order')
            ->get();

        return view('public.work.index', [
            'projects' => $projects,
            'industries' => Industry::orderBy('name')->get(),
            'services' => Service::whereNotNull('pillar')->orderBy('name')->get(),
        ]);
    }

    public function show(Project $project)
    {
        abort_unless($project->is_published, 404);

        $project->load(['industry', 'client', 'services', 'images', 'metrics', 'caseStudyDetail']);

        return view('public.work.show', [
            'project' => $project,
            'relatedProjects' => $this->relatedProjects($project),
            'previousProject' => $this->adjacentProject($project, 'previous'),
            'nextProject' => $this->adjacentProject($project, 'next'),
        ]);
    }

    private function relatedProjects(Project $project)
    {
        return Project::query()
            ->published()
            ->where('id', '!=', $project->id)
            ->where(function ($q) use ($project) {
                $q->where('industry_id', $project->industry_id)
                    ->orWhere('project_type', $project->project_type);
            })
            ->with('industry')
            ->orderBy('sort_order')
            ->limit(3)
            ->get();
    }

    private function adjacentProject(Project $project, string $direction): ?Project
    {
        $publishedIds = Project::published()->orderBy('sort_order')->pluck('id')->values();
        $currentIndex = $publishedIds->search($project->id);

        if ($currentIndex === false) {
            return null;
        }

        $targetIndex = $direction === 'previous' ? $currentIndex - 1 : $currentIndex + 1;

        if (! $publishedIds->has($targetIndex)) {
            return null;
        }

        return Project::find($publishedIds->get($targetIndex));
    }
}