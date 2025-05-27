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

/** ProjectController::index() method */

test("that index method should return 3 projects to frontend", function () {
    // expected values
    $projects = Project::all();

    $this->get(route('projects'))->assertOk()
    ->assertInertia(
        fn (Assert $page) =>
            assertMyModels([
                fn () => [$page, 'projects', 'projects/projects', $projects, ['id', 'name']],
            ])
    );
});


/** Create method */

test('Create method should work correctly', function () {
    // expected values

    $this->post(route('project.create'))->assertOk()
    ->assertInertia(
        fn (Assert $page) =>
        $page->component('projects/create_edit')
    );
});

// /** Store method */

test('Store method should work correctly', function () {
    // simulate creating task under 3rd project
    $dateTime = '2025-05-23T14:09:34.283Z'; // frontend send the datetime in UTC format
    // convert UTC datetime format to what will be stored in database
    $expectedUtc = Carbon::parse($dateTime)->format('Y-m-d H:i:s');

    $formData = [
        'name'=> 'Test Project 1',
        'created_at'=> $dateTime,
        'updated_at'=> $dateTime,
    ];

    $this->post(route('project.store'), $formData)->assertStatus(302)
    ->assertRedirect(route('projects'));

    $this->assertDatabaseHas('projects', [
        'name'=> $formData['name'],
        'created_at'=> $expectedUtc,
        'updated_at'=> $expectedUtc,
    ]);
});
it('simulates DB exception for store method', function () {
    $mock = Mockery::mock(Project::class);
    $mock->shouldReceive('create')->andThrow(new Exception('Simulated error'));

    $this->app->instance(Project::class, $mock);

    $dateTime = '2025-05-23T14:09:34.283Z';
    $formData = [
        'name'=> 'Test Project 1',
        'created_at'=> $dateTime,
        'updated_at'=> $dateTime,
    ];

    $this->post(route('project.store'), $formData)->assertRedirect(route('projects'))
      ->assertSessionHas('message', 'Simulated error');
});

// /** Edit method */

test('Edit method should work correctly', function () {
    // expected values
    $project = Project::findOrFail(3);

    $this->get(route('project.edit',3))->assertOk()
    ->assertInertia(
        fn (Assert $page) =>
            assertMyModels([
                fn () => [$page, 'projectToEdit', 'projects/create_edit', $project, ['id', 'name']],
            ])
    );
});

test('Simulate db exception in Edit method', function () {
    $projectId = 2;
    $mock = Mockery::mock(Project::class);
    $mock->shouldReceive('findOrFail')->with($projectId)->andThrow(new Exception('Simulated DB Error'));

    $this->app->instance(Project::class, $mock);

    $this->get(route('project.edit',$projectId))->assertStatus(302)
    ->assertRedirect(route('projects'))
    ->assertSessionHas('message','Simulated DB Error');
});

test('Update method should work correctly', function () {
    // simulate updating task 2 under project 1
    $dateTime = '2025-05-23T14:11:30.283Z'; // frontend send the datetime in UTC format
    // convert UTC datetime format to what will be stored in database
    $expectedUtc = Carbon::parse($dateTime)->format('Y-m-d H:i:s');

    $formData = [
        'id' => 2,
        'name'=> 'Project 2 updated',
        'created_at'=> $dateTime,
        'updated_at'=> $dateTime,
    ];

    $this->put(route('project.update',2), $formData)->assertStatus(302)
    ->assertRedirect(route('projects'));

    $this->assertDatabaseHas('projects', [
        'id'=> $formData['id'],
        'name'=> $formData['name'],
        'created_at'=> $expectedUtc,
        'updated_at'=> $expectedUtc,
    ]);
});

test('Destroy method should work correctly', function () {
    // simulate deleting project 2
    $this->delete(route('project.destroy',2))->assertStatus(302)
    ->assertRedirect(route('projects'))
    ->assertSessionHas('message','Successfully deleted the project.');

    $this->assertDatabaseMissing('projects', [
        'id'=> 2,
        'name'=> 'Project 2',
    ]);
});
test('Simulate Db Exception in Destroy method', function () {
    $projectId=2;
    $mock = Mockery::mock(Project::class);
    $mock->shouldReceive('findOrFail')->with($projectId)->andReturn($mock);
    $mock->shouldReceive('delete')->once()->andThrow(new Exception('Simulated DB Exception'));

    $this->app->instance(Project::class, $mock);

    // simulate deleting project 2
    $this->delete(route('project.destroy',$projectId))->assertStatus(302)
    ->assertRedirect(route('projects'))
    ->assertSessionHas('message','Simulated DB Exception');

    $this->assertDatabaseHas('projects', [
        'id'=> 2,
        'name'=> 'Project 2',
    ]);
});
