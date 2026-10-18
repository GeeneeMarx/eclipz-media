<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ClientPartner;

class ClientsController extends Controller
{
    public function index()
    {
        $visible = ClientPartner::visible()->orderBy('sort_order')->get();

        return view('public.clients', [
            'partners' => $visible->where('type', 'partner'),
            'clients' => $visible->where('type', 'client'),
        ]);
    }
}