<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Http\Requests\ProjectFormRequest;
use Illuminate\Support\Facades\Redirect;
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

    public function create()
    {
        return Inertia::render('project_create_edit');
    }
    public function store(ProjectFormRequest $request)
    {
        try {
            Project::create($request->validated());
            return Redirect::route('projects')->with('message','Succesfully created project');
        } catch (\Exception $e) {
            return Redirect::route('projects')->with('message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $project = Project::findOrFail($id);
            return Inertia::render('project_create_edit')->with('projectToEdit', $project);
        } catch (\Exception $e) {
            return Redirect::route('projects')->with('message', $e->getMessage());
        }
    }
    public function update(ProjectFormRequest $request, $id)
    {
        try {
            $project = Project::findOrFail($id);
            $project->update($request->validated());
            return Redirect::route('projects')->with('message','Successfully updated the project');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $project = Project::findOrFail($id);
            $project->delete();
            return Redirect::route('projects')->with('message','Successfully deleted the project.');
        } catch (\Exception $e) {
            return Redirect::route('projects')->with('message', $e->getMessage());
        }
    }
}
