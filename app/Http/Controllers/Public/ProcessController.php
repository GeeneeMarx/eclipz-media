<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

/**
 * The standalone Process page was retired. The steps now live on the homepage (a short
 * overview) and on the Services page (the full list). This permanent redirect keeps old
 * links, bookmarks and search results working.
 */
class ProcessController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect('/services#how-we-work', 301);
    }
}