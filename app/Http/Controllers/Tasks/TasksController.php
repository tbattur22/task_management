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
use App\Services\TaskService;

class TasksController extends Controller
{
    /**
     * Returns list of all tasks under the selected project ordered by priority
     * @return \Inertia\Response
     */
    public function index()
    {
        $projects = Project::all();
        $tasks = null;
        // check if any project was selected by user otherwise select the 1st project if available
        $projectId = session()->get('project_id') ?? ( $projects[0] ?? null);
        if (is_object($projectId)) {
            $projectId = $projectId->id;
        }

        if ($projectId) {
            $selectedProject = Project::findOrFail($projectId);
            $tasks = $selectedProject->tasks()->orderBy('priority','asc')->get();
        } else {
            $selectedProject = null;
        }

        return Inertia::render('home', [
            'projects'=> $projects,
            'selectedProject'=> $selectedProject,
            'tasks'=> $tasks
        ]);
    }

    /**
     * Sets selected project and returns list of all tasks belonging to this project
     * @param int $projectId
     * @throws \Exception
     * @return \Inertia\Response
     */
    public function selectProject(int $projectId)
    {
        $projects = Project::all();

        // Get the tasks belonging to the selected project
        if ($projectId > 0) {
            $project = Project::findOrFail($projectId);
            $tasks = Task::where('project_id', $projectId)->orderBy('priority')->get();
            // store the selected project id in the session
            session()->put('project_id', $projectId);
        } else {
            throw new \Exception('project_id parameter must be positive number');
        }

        return Inertia::render('home', [
            'projects'=> $projects,
            'selectedProject'=> $project,
            'tasks'=> $tasks
        ]);
    }

    /**
     * Displays the form to create a new task.
     * @param int $projectId
     * @return \Inertia\Response
     */
    public function create(int $projectId)
    {
        try {
            // get loaded Project class from container so that we can mock it in Pest test
            $project = app(Project::class)->findOrFail($projectId);
            // pass null as task to the React component to indicate it is new task creation
            return Inertia::render('tasks/create_edit')->with('project', $project)->with('taskToEdit', null);
        } catch (\Exception $e) {
            return Redirect::route('home')->with('message', $e->getMessage());
        }
    }

    /**
     * Stores the new task in the database after validating the form values
     * @param \App\Http\Requests\TaskFormRequest $request
     * @param \App\Services\TaskService $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TaskFormRequest $request, TaskService $service)
    {
        try {
            // using TaskService to create task for easy mocking in Pest test
            $newTask = $service->create($request->validated());

            if ($newTask instanceof Task) {
                return Redirect::route('home')->with('message','Succesfully created task');
            } else {
                return Redirect::route('home')->with('message','Failed to create the task');
            }
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
            $task = app(Task::class)->findOrFail($id);
            $project = Project::findOrFail($task->project_id);
            return Inertia::render('tasks/create_edit')->with('project', $project)->with('taskToEdit', $task);
        } catch (\Exception $e) {
            return Redirect::route('home')->with('message', $e->getMessage());
        }
    }

    /**
     * Method updates the Task model (uses route model binding)
     *
     * @param \App\Http\Requests\TaskFormRequest $request
     * @param \App\Models\Task $task
     * @param \App\Services\TaskService $service
     * @return \Illuminate\Http\RedirectResponse|\Inertia\Response
     */
    public function update(TaskFormRequest $request, Task $task, TaskService $service)
    {
        try {
            $service->update($task, $request->validated());
            return Redirect::route('home')->with('message','Succesfully updated the task');
        } catch (\Exception $e) {
            Log::error('DB Error on task update: ' . $e->getMessage());
            return redirect()->back()->with('message', $e->getMessage());
        }
    }

    /**
     * Deletes the task
     * @param int $id task id to delete
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $id)
    {
        try {
            $task = app(Task::class)->findOrFail($id);
            $task->delete();

            return Redirect::route('home')->with('message','Successfully deleted the task.');
        } catch (\Exception $e) {
            Log::error('DB error on task destroy'. $e->getMessage());
            return Redirect::route('home')->with('message', $e->getMessage());
        }
    }

    /**
     * Updates the priorities of the tasks as per passed in ordered task ids
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function updatePriority(Request $request)
    {
        // get the comma separated ordered task ids
        $data = json_decode($request->getContent(), true);
        // update the tasks table with the new priority orders
        $res = Task::updatePriorities(explode(',',$data['newlySortedIds']));

        return response()->json($res);
    }
}
