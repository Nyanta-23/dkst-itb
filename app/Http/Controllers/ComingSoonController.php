<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ComingSoonController extends Controller
{
    public function __invoke(Request $request): Response
    {
        return Inertia::render('coming-soon', [
            'title' => $request->route('title'),
            'icon' => $request->route('icon'),
            'description' => $request->route('description'),
        ]);
    }
}
