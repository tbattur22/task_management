<?php

test('Guest should be redirected to login page', function () {
    $this->get('/')->assertRedirect('/login');
    $this->get('/projects')->assertRedirect('/login');
    $this->get('/tasks')->assertRedirect('/login');
    $this->get('/anyroute')->assertRedirect('/login');
});
