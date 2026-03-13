<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService,
    ) {
    }

    public function index(Request $request): View
    {
        $dashboard = $this->taskService->dashboard($request->user());

        return view('dashboard.index', [
            'stats' => $dashboard['stats'],
            'chart' => $dashboard['chart'],
        ]);
    }
}
