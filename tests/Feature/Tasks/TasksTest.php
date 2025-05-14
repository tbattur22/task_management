<?php

use App\Models\Project;
use Inertia\Testing\AssertableInertia as Assert;

// beforeAll()
// {
//     $this->user = factory(User::class)->create();
// }

test('Guest should be redirected to login page', function () {
    $this->get('/')->assertRedirect('/login');
});

test("Logged in user should get all existing tasks", function () {
    $res = login()->get('/');
    $res->assertStatus(200);
});

test('Logged in user should get only the tasks', function () {
    Project::factory()->count(3)->create();

    $res = login()->post('/tasks/select_project/2');
    $res->assertStatus(200);

});
