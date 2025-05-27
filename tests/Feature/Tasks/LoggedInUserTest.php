<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Carbon;
use App\Services\TaskService;
use Illuminate\Database\QueryException;

beforeEach(function() {
    // uses(RefreshDatabase::class);
    uses(DatabaseMigrations::class);// migrate:fresh resets auto_increments

    $this->user = User::factory()->create();
    $project1 =
        Project::factory()
        ->has(
            Task::factory(3)
            ->sequence(
                ['name'=>'Task 1 of project 1','priority'=>1],
                ['name'=>'Task 2 of project 1','priority'=>2],
                ['name'=>'Task 3 of project 1','priority'=>3]
            ))
        ->create(['name'=>'Project 1']);

    $project2 =
        Project::factory()
        ->has(
            Task::factory(2)
            ->sequence(
                ['name'=>'Task 1 of project 2','priority'=>1],
                ['name'=>'Task 2 of project 2','priority'=>2],
            ))
        ->create(['name'=>'Project 2']);

    $project3 = Project::factory()->create(['name'=>'Project 3']);
    $this->projects = collect([$project1, $project2, $project3]);

    $this->actingAs($this->user);
});

/** TasksController::index() method */

test("user has not selected any project yet, so index method should return 3 projects, 3 tasks under project 1 to frontend", function () {
    // expected values
    $projects = Project::all();
    $selectedProject = $projects->first();
    $tasks = $selectedProject->tasks()->get();

    $this->get(route('home'))->assertOk()
    ->assertInertia(
        fn (Assert $page) =>
            assertMyModels([
                fn () => [$page, 'projects', 'home', $projects, ['id', 'name']],
                fn () => [$page, 'selectedProject', 'home', $selectedProject, ['id', 'name']],
                fn () => [$page, 'tasks', 'home', $tasks, ['id', 'name', 'priority', 'project_id']],
            ])
    );
});

test("user has selected project 2 which has 2 tasks, so index method should return 3 projects, 2 tasks under project 2 to frontend", function () {
    // expected values
    $projects = Project::all();
    $selectedProject = $projects->get(1);
    $tasks = $selectedProject->tasks()->get();

    $this->withSession(['project_id'=>2])->get(route('home'))->assertOk()
    ->assertInertia(
        fn (Assert $page) =>
            assertMyModels([
                fn () => [$page, 'projects', 'home', $projects, ['id', 'name']],
                fn () => [$page, 'selectedProject', 'home', $selectedProject, ['id', 'name']],
                fn () => [$page, 'tasks', 'home', $tasks, ['id', 'name', 'priority', 'project_id']],
            ])
    );
});

test('Select project method should work correctly', function () {
    // expected values
    $projects = Project::all();
    // simulate the 2nd project was selected
    $selectedProject = $projects->get(1);
    $tasks = $selectedProject->tasks()->get();


    $this->post(route('project.select',2))->assertOk()
    ->assertInertia(
        fn (Assert $page) =>
            assertMyModels([
                fn () => [$page, 'projects', 'home', $projects, ['id', 'name']],
                fn () => [$page, 'selectedProject', 'home', $selectedProject, ['id', 'name']],
                fn () => [$page, 'tasks', 'home', $tasks, ['id', 'name', 'priority', 'project_id']],
            ])
    );
});

/** Create method */

test('Create method should work correctly', function () {
    // expected values
    // simulate creating task under 3rd project
    $project = Project::findOrFail(3);

    $this->post(route('task.create',3))->assertOk()
    ->assertInertia(
        fn (Assert $page) =>
            assertMyModels([
                fn () => [$page, 'project', 'tasks/create_edit', $project, ['id', 'name']],
            ])
            ->has('taskToEdit', null)
    );
});
it('simulates exception during Task::create', function () {
    $projectId = 3;
    $expectedExceptionMsg = 'Simulated DB error (Connection: mysql, SQL: select * from projects where id =3)';
    $exception = new QueryException(
        'mysql',
        'select * from projects where id =?', // SQL
        [$projectId], // Bindings
        new Exception('Simulated DB error') // Previous Exception
    );

    // Mock the Project model
    $mock = Mockery::mock(Project::class);
    $mock->shouldReceive('findOrFail')->with($projectId)->andThrow($exception);

    // Replace the Project binding in the container
    app()->instance(Project::class, $mock);

    $response = $this->post(route('task.create',3))->assertStatus(302);
    $response->assertRedirect(route('home'));
    $response->assertSessionHas('message', $expectedExceptionMsg);
});

/** Store method */

test('Store method should work correctly', function () {
    // simulate creating task under 3rd project
    $dateTime = '2025-05-23T14:09:34.283Z'; // frontend send the datetime in UTC format
    // convert UTC datetime format to what will be stored in database
    $expectedUtc = Carbon::parse($dateTime)->format('Y-m-d H:i:s');

    $formData = [
        'name'=> 'Test Task 1 under project 3',
        'priority'=> 1,
        'project_id'=> 3,
        'created_at'=> $dateTime,
        'updated_at'=> $dateTime,
    ];

    $this->post(route('task.store'), $formData)->assertStatus(302)
    ->assertRedirect(route('home'));

    $this->assertDatabaseHas('tasks', [
        'name'=> $formData['name'],
        'priority'=> $formData['priority'],
        'project_id'=> $formData['project_id'],
        'created_at'=> $expectedUtc,
        'updated_at'=> $expectedUtc,
    ]);
});
it('simulates DB exception from service for store method', function () {
    $mock = Mockery::mock(TaskService::class);
    $mock->shouldReceive('create')->andThrow(new Exception('Simulated error'));

    $this->app->instance(TaskService::class, $mock);

    $dateTime = '2025-05-23T14:09:34.283Z';
    $formData = [
        'name'=> 'Test Task 1 under project 3',
        'priority'=> 1,
        'project_id'=> 3,
        'created_at'=> $dateTime,
        'updated_at'=> $dateTime,
    ];

    $this->post(route('task.store'), $formData)->assertRedirect(route('home'))
      ->assertSessionHas('message', 'Simulated error');
});

/** TaskFormRequest validations */
test('Store method validations, task name is empty and passed in existing priority for POST request (creating)', function () {
    // simulate creating task under 3rd project
    $dateTime = '2025-05-23T14:09:34.283Z'; // frontend send the datetime in UTC format
    // convert UTC datetime format to what will be stored in database
    $expectedUtc = Carbon::parse($dateTime)->format('Y-m-d H:i:s');
    $project = Project::findOrFail(2);
    $task = $project->tasks->get(0);

    $formData = [
        'name'=> '',
        'priority'=> 1,
        'project_id'=> 2,
        'created_at'=> $dateTime,
        'updated_at'=> $dateTime,
    ];

    $this->post(route('task.store'), $formData)->assertStatus(422)
    ->assertInertia(
        fn (Assert $page) =>
            assertMyModels([
                fn () => [$page, 'project', 'tasks/create_edit', $project, ['id', 'name']],
            ])->has('taskToEdit', null)
            ->where('errors', [
                'name'=>['The name field is required.'],
                'priority'=>['The priority has already been taken.'],
            ])

    );

    $this->assertDatabaseMissing('tasks', [
        'name'=> $formData['name'],
        'priority'=> $formData['priority'],
        'project_id'=> $formData['project_id'],
        'created_at'=> $expectedUtc,
        'updated_at'=> $expectedUtc,
    ]);
});

/** Edit method */

test('Edit method should work correctly', function () {
    // expected values
    // simulate creating task under 3rd project
    $task = Task::findOrFail(2);
    $project = $task->project;

    $this->get(route('task.edit',2))->assertOk()
    ->assertInertia(
        fn (Assert $page) =>
            assertMyModels([
                fn () => [$page, 'project', 'tasks/create_edit', $project, ['id', 'name']],
                fn () => [$page, 'taskToEdit', 'tasks/create_edit', $task, ['id', 'name','priority', 'project_id']],
            ])

    );
});

test('Simulate db exception in Edit method', function () {
    $taskId = 2;
    $mock = Mockery::mock(Task::class);
    $mock->shouldReceive('findOrFail')->with($taskId)->andThrow(new Exception('Simulated DB Error'));

    $this->app->instance(Task::class, $mock);

    $this->get(route('task.edit',$taskId))->assertStatus(302)
    ->assertRedirect(route('home'))
    ->assertSessionHas('message','Simulated DB Error');
});

test('Update method should work correctly', function () {
    // simulate updating task 2 under project 1
    $dateTime = '2025-05-23T14:11:30.283Z'; // frontend send the datetime in UTC format
    // convert UTC datetime format to what will be stored in database
    $expectedUtc = Carbon::parse($dateTime)->format('Y-m-d H:i:s');

    $formData = [
        'id' => 2,
        'name'=> 'Task 2 of project 1 updated',
        'priority'=> 2,
        'project_id'=> 1,
        'created_at'=> $dateTime,
        'updated_at'=> $dateTime,
    ];

    $this->put(route('task.update',2), $formData)->assertStatus(302)
    ->assertRedirect(route('home'));

    $this->assertDatabaseHas('tasks', [
        'id'=> $formData['id'],
        'name'=> $formData['name'],
        'priority'=> $formData['priority'],
        'project_id'=> $formData['project_id'],
        'created_at'=> $expectedUtc,
        'updated_at'=> $expectedUtc,
    ]);
});

test('Destroy method should work correctly', function () {
    // simulate deleting task 2 under project 1
    $this->delete(route('task.destroy',2))->assertStatus(302)
    ->assertRedirect(route('home'))
    ->assertSessionHas('message','Successfully deleted the task.');

    $this->assertDatabaseMissing('tasks', [
        'id'=> 2,
        'name'=> 'Task 2 of project 1',
        'priority'=> 2,
        'project_id'=> 1,
    ]);
});
test('Simulate Db Exception in Destroy method', function () {
    $taskId=2;
    $mock = Mockery::mock(Task::class);
    $mock->shouldReceive('findOrFail')->with($taskId)->andReturn($mock);
    $mock->shouldReceive('delete')->once()->andThrow(new Exception('Simulated DB Exception'));

    $this->app->instance(Task::class, $mock);

    // simulate deleting task 2 under project 1
    $this->delete(route('task.destroy',$taskId))->assertStatus(302)
    ->assertRedirect(route('home'))
    ->assertSessionHas('message','Simulated DB Exception');

    $this->assertDatabaseHas('tasks', [
        'id'=> 2,
        'name'=> 'Task 2 of project 1',
        'priority'=> 2,
        'project_id'=> 1,
    ]);
});

test('updatePriority method should work correctly', function () {
    // simulate moved task 3 to the top of the list for project 1
    $data = ["newlySortedIds" => '3,1,2'];// it was 1,2,3
    $this->postJson(route('task.updatePriority'), $data)->assertOk()
    ->assertJson([
        'status' => 'success'
    ]);

    $this->assertDatabaseHas('tasks', [
        'id'=> 3,
        'name'=> 'Task 3 of project 1',
        'priority'=> 1,
        'project_id'=> 1,
    ]);
    $this->assertDatabaseHas('tasks', [
        'id'=> 1,
        'name'=> 'Task 1 of project 1',
        'priority'=> 2,
        'project_id'=> 1,
    ]);
    $this->assertDatabaseHas('tasks', [
        'id'=> 2,
        'name'=> 'Task 2 of project 1',
        'priority'=> 3,
        'project_id'=> 1,
    ]);
});
