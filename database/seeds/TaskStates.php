<?php

use Illuminate\Database\Seeder;

use App\Http\Models\TaskState;

class TaskStates extends Seeder{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        $d = date("Y-m-d H:i:s");
        TaskState::insert([
                    ['name' => 'Pending','created_at' => $d,'updated_at' => $d],
                    ['name' => 'Completed','created_at' => $d,'updated_at' => $d]
                ]);
    }
}
