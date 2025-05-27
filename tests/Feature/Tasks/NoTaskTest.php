<?php

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function() {
    $this->user = User::factory()->create();
    login($this->user);
    $this->actingAs($this->user);
    $this->project = Project::factory(3)->create();
});

test("Nothing is sent to React when no task exist", function () {
    $res = $this->get(route('home'))->assertOk();
    $res->assertInertia(
        fn (Assert $page) =>
        $page->component('home')
        ->has('projects', null)
        ->has('selectedProject', null)
        ->has('tasks', null)
    );
    // should redirect to home route as the requested task does not exist
    $res = $this->get(route('task.edit', 1))->assertStatus(302);

    /** POST, PUT DELETE methods */
    $res = $this->get(route('task.create', 1))->assertStatus(302);
    $res = $this->get(route('task.store', 1))->assertStatus(302);
    $res = $this->get(route('task.update', 1))->assertStatus(302);
    $res = $this->get(route('task.destroy', 1))->assertStatus(302);

    /** select project and change task priority */
    $res = $this->get(route('project.select', 1))->assertStatus(302);
    $res = $this->get(route('task.updatePriority', [1,2]))->assertStatus(302);
});
