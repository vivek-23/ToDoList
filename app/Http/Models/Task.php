<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Models\TaskState;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model{
    use SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'task';
    public $timestamps = true;

    private const PENDING_STATE = 1; // primary key ID of pending in TaskState
    private const COMPLETED_STATE = 2; // primary key ID of completed in TaskState

    public function taskState(){
        return $this->hasOne(TaskState::class, 'task_status');
    }

    public function subTasks(){
        return $this->hasMany(Task::class, 'parent_id');
    }

    //accessor
    public function getDueDateAttribute($value){
        return date("Y/m/d", strtotime($value));
    }

    public static function create($data){
        $row = new self();
        $row->title = $data['title'];
        $row->due_date = $data['due_date'];
        $row->task_status = self::PENDING_STATE;
        if(!empty($data['parent_task'])){
            $row->parent_id = $data['parent_task'];
        }
        $row->save();
        return $row;
    }

    public static function complete(Task $task){
        $task->task_status = self::COMPLETED_STATE;
        $task->save();
        $task->subTasks()->update(['task_status' => self::COMPLETED_STATE]);
    }

    public static function deleteRelated(Task $task){
        $task->delete();
        $task->subTasks()->delete();
    }

    public function isPending(){
        return $this->task_status == self::PENDING_STATE;
    }

    public function isCompleted(){
        return $this->task_status == self::COMPLETED_STATE;
    }

    public static function getPendingStatus(){
        return self::PENDING_STATE;
    }

    public static function getCompletedStatus(){
        return self::COMPLETED_STATE;
    }
}
