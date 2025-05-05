<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['name' => 'Task 1','priority'=> 1,'project_id'=> 1],
            ['name' => 'Task 2','priority'=> 2,'project_id'=> 1],
            ['name' => 'Task 3','priority'=> 3,'project_id'=> 1],
            ['name' => 'Task 1','priority'=> 1,'project_id'=> 2],
            ['name' => 'Task 2','priority'=> 2,'project_id'=> 2],
            ['name' => 'Task 1','priority'=> 1,'project_id'=> 3],
        ];

        Task::insert($rows);
    }
}
