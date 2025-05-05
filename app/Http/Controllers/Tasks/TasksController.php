<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Requests\TaskFormRequest;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TasksController extends Controller
{
    public function index()
    {
        $projects = Project::all();
        $tasks = null;
        // select 1st project if available
        $selectedProject = $projects[0] ?? null;
        if ($selectedProject) {
            $tasks = Task::where('project_id', $selectedProject->id)->orderBy("priority","asc")->get();
        }
        return Inertia::render('home', [
            'projects'=> $projects,
            'selectedProject'=> $selectedProject,
            'tasks'=> $tasks
        ]);
    }

    public function selectProject(Request $request, int $projectId)
    {
        $projects = Project::all();

        // Get the tasks belonging to the selected project
        if ($projectId > 0) {
            $project = Project::findOrFail($projectId);
            $tasks = Task::where('project_id', $projectId)->orderBy('priority')->get();
        } else {
            throw new \Exception('project_id parameter must be positive number');
        }
        return Inertia::render('home', [
            'projects'=> $projects,
            'selectedProject'=> $project,
            'tasks'=> $tasks
        ]);
    }

    public function create()
    {
        // pass null as task to the React component to indicate it is new task creation
        return Inertia::render('task_create_edit')->with('projects', Project::all())->with('taskToEdit', null);
    }
    public function store(TaskFormRequest $request)
    {
        try {
            Task::create($request->validated());
            return Redirect::route('home')->with('message','Succesfully created task');
        } catch (\Exception $e) {
            return Redirect::route('home')->with('message', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $task = Task::findOrFail($id);
            return Inertia::render('task_create_edit')->with('projects', Project::all())->with('taskToEdit', $task);
        } catch (\Exception $e) {
            return Redirect::route('home')->with('message', $e->getMessage());
        }
    }
    public function update(TaskFormRequest $request, $id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->update($request->validated());
            return Redirect::route('home')->with('message','Successfully updated the task');
        } catch (\Exception $e) {
            return redirect()->back()->with('message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $task->delete();
            return Redirect::route('home')->with('message','Successfully deleted the task.');
        } catch (\Exception $e) {
            return Redirect::route('home')->with('message', $e->getMessage());
        }
    }
}
