<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\Task;

class TaskState extends Model{
    protected $primaryKey = 'id';
    protected $table = 'task_state';
    public $timestamps = true;

    public function tasks(){
        return $this->hasMany(Task::class, 'task_status');
    }
}
