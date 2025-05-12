<?php

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Http\Requests\TaskFormRequest;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TasksController extends Controller
{
    public function index()
    {
        Log::info("TasksController:index()");
        $projects = Project::all();
        $tasks = null;
        // check if any project was selected by user otherwise select the 1st project if available
        $projectId = session()->get('project_id') ?? ( $projects[0] ?? null);
        if (is_object($projectId)) {
            $projectId = $projectId->id;
        }
        // Log::info("TasksController:index(): project id is ". $projectId ."");
        if ($projectId) {
            $selectedProject = Project::findOrFail($projectId);
            $tasks = Task::where('project_id', $selectedProject->id)->orderBy("priority","asc")->get();
            // Log::info("TasksController:index():selectedProject {$selectedProject->name} and there are {count($tasks)}");
        } else {
            $selectedProject = null;
            // Log::info("TasksController:index():project id is NULL");
        }

        // LOG::info("TasksController:index(): before returning Inertia home component");
        return Inertia::render('home', [
            'projects'=> $projects,
            'selectedProject'=> $selectedProject,
            'tasks'=> $tasks
        ]);
    }

    public function selectProject(int $projectId, string $flashMessage = '')
    {
        $projects = Project::all();

        // Get the tasks belonging to the selected project
        if ($projectId > 0) {
            $project = Project::findOrFail($projectId);
            $tasks = Task::where('project_id', $projectId)->orderBy('priority')->get();
            session()->put('project_id', $projectId);
        } else {
            throw new \Exception('project_id parameter must be positive number');
        }
        return Inertia::render('home', [
            'projects'=> $projects,
            'selectedProject'=> $project,
            'tasks'=> $tasks
        ])->with('message', $flashMessage);
    }

    public function create(int $projectId)
    {
        Log::info('TasksController:create():projectId'. $projectId);
        $project = Project::findOrFail($projectId);
        // pass null as task to the React component to indicate it is new task creation
        return Inertia::render('task_create_edit')->with('project', $project)->with('taskToEdit', null);
    }
    public function store(TaskFormRequest $request)
    {
        Log::info('TasksController:store()');

        try {
            DB::listen(function ($query) {
                Log::debug("SQL: {$query->sql}", $query->bindings);
            });

            $newTask = Task::create($request->validated());

            if ($newTask instanceof Task) {
                return Redirect::route('home')->with('message','Succesfully created task');
            } else {
                return Redirect::route('home')->with('message','Failed to create the task');
            }
            // return $this->selectProject($request->project_id, "Successfully created task");
            // return Redirect::route('project.select')->with('id', $request->project_id)->with('message','Successfully created task');
        } catch (\Exception $e) {
            Log::error('DB Error on task create: ' . $e->getMessage());
            return Redirect::route('home')->with('message', $e->getMessage());
        }
    }

    /**
     * Method displays the task edit form (also used for task create)
     *
     * @param mixed $id task id
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function edit($id)
    {
        try {
            $task = Task::findOrFail($id);
            $project = Project::findOrFail($task->project_id);
            return Inertia::render('task_create_edit')->with('project', $project)->with('taskToEdit', $task);
        } catch (\Exception $e) {
            return Redirect::route('home')->with('message', $e->getMessage());
        }
    }
    /**
     * Method updates the Task model (uses route model binding)
     *
     * @param \App\Http\Requests\TaskFormRequest $request
     * @param \App\Models\Task $task
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function update(TaskFormRequest $request, Task $task)
    {
        try {
            $task->update($request->validated());
            return Redirect::route('home')->with('message','Succesfully updated the task');
            // return Redirect::route('home')->with('message','Successfully updated the task');
            // return $this->selectProject($task->project_id, "Successfully updated the task");
        } catch (\Exception $e) {
            return redirect()->back()->with('message', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $task = Task::findOrFail($id);
            $projectId = $task->project_id;
            $task->delete();
            return Redirect::route('home')->with('message','Successfully deleted the task.');
            // return $this->selectProject($projectId, "Successfully deleted the task");
        } catch (\Exception $e) {
            return Redirect::route('home')->with('message', $e->getMessage());
        }
    }

    public function updatePriority(Request $request)
    {
        $content = $request->getContent();
        Log::info($content);

        $res = Task::updatePriorities(explode(',',$content));
        Log::info(print_r($res, true));

        return response()->json($res);
    }
}
