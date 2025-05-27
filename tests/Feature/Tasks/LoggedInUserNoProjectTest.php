<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Illuminate\Support\Carbon;

beforeEach(function() {
    // uses(RefreshDatabase::class);
    uses(DatabaseMigrations::class);// migrate:fresh resets auto_increments

    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test("index method should return null for projects,tasks and selectedProject", function () {
    // expected values
    $projects = null;
    $selectedProject = null;
    $tasks = null;

    $this->get(route('home'))->assertOk()
    ->assertInertia(
        fn (Assert $page) =>
        $page->has('projects', null)
        ->has('tasks', null)
        ->has('selectedProject', null)
    );
});
