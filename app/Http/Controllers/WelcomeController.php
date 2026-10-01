<?php

namespace App\Http\Controllers;

use App\Models\IpAsset;
use App\Models\Partner;
use App\Models\Technology;
use App\Models\Tenant;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('welcome', [
            'stats' => [
                'technologies' => Technology::count(),
                'ipAssets' => IpAsset::count(),
                'partners' => Partner::count(),
                'tenants' => Tenant::count(),
            ],
        ]);
    }
}
