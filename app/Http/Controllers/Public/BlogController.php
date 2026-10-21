<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $categories = Post::query()
            ->live()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // Only a category that really has live posts is used. Anything else is ignored.
        $requested = $request->query('category');
        $activeCategory = is_string($requested) && $categories->contains($requested) ? $requested : null;

        $posts = Post::query()
            ->live()
            ->with('author')
            ->when($activeCategory, fn ($query, $category) => $query->where('category', $category))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        // A page number beyond the end goes to the last real page.
        if ($posts->isEmpty() && $posts->currentPage() > 1 && $posts->lastPage() > 0) {
            return redirect($posts->url($posts->lastPage()));
        }

        return view('public.blog.index', [
            'posts' => $posts,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
        ]);
    }

    public function show(Post $post): Response
    {
        $isLive = $post->isLive();

        // Drafts, scheduled posts and switched-off posts are invisible to everyone except
        // a signed-in, active admin previewing their own work.
        abort_unless($isLive || $this->canPreviewDrafts(), 404);

        $post->load('author');

        $response = response()->view('public.blog.show', [
            'post' => $post,
            'isPreview' => ! $isLive,
            'morePosts' => Post::query()
                ->live()
                ->where('id', '!=', $post->id)
                ->with('author')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(3)
                ->get(),
        ]);

        return $isLive ? $response : $response->header('Cache-Control', 'no-store, private');
    }

    private function canPreviewDrafts(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->canAccessPanel(Filament::getPanel('admin'));
    }
}