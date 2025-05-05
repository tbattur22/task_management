<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['name' => 'Task Management Project 1'],
            ['name' => 'Task Management Project 2'],
            ['name' => 'Task Management Project 3'],
        ];

        Project::insert($rows);
    }
}
