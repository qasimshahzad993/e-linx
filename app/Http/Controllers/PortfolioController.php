<?php

namespace App\Http\Controllers;

use App\Models\Portfolio;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function show(Portfolio $project): View
    {
        abort_unless($project->status, 404);

        return view('portfolio.show', compact('project'));
    }
}
