<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use Inertia\Inertia;

class ProjectsController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        return Inertia::render('projects', [
            'projects'=> $projects,
        ]);
    }
}
