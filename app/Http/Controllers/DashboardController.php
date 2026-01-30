<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {
    }

    public function __invoke(Request $request): View
    {
        $dashboardData = $this->dashboardService->getStatistics(Auth::user());

        return view('dashboard', [
            'totalCombats' => $dashboardData->totalCombats,
            'activeCombats' => $dashboardData->activeCombats,
            'totalCharacters' => $dashboardData->totalCharacters,
            'recentCombats' => $dashboardData->recentCombats,
        ]);
    }
}
