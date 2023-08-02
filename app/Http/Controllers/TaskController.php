<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Task;
use Illuminate\Support\Facades\Validator;

use App\Rules\DueDateFormat;

use DateTime;

class TaskController extends Controller{
    public function create(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100',
            'due_date' => ['required', new DueDateFormat],
            'parent_task' => 'nullable|integer'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        if(!empty($request->get('parent_task'))){
            $parent = Task::find($request->get('parent_task'));
            if(empty($parent)){
                return response()->json([
                    'success' => false,
                    'message' => 'No such parent task found.'
                ]);
            }else{
                $d1 = DateTime::createFromFormat('Y/m/d', $parent->due_date);
                $givenD = DateTime::createFromFormat('Y/m/d', $request->get('due_date'));
                if($givenD > $d1){
                    return response()->json([
                        'success' => false,
                        'message' => "Subtask's due date cannot be greater than parent task's due date."
                    ]);
                }
            }
        }       

        $task = Task::create($request->only('title','due_date','parent_task'));

        return response()->json([
                'success' => true,
                'message' => 'New task with title '. $request->get('title') .' is created successfully.',
                'id' => $task->id
            ]);
    }

    public function complete($id){
        $task = Task::find($id);

        if(empty($task)){
            return response()->json([
                'success' => false,
                'message' => 'No such task found.'
            ]);
        }

        Task::complete($task);

        return response()->json([
            'success' => true,
            'message' => 'The task is marked as completed successfully along with all it\'s subtasks.'
        ]);
    }

    public function delete($id){
        $task = Task::find($id);

        if(empty($task)){
            return response()->json([
                'success' => false,
                'message' => 'No such task found.'
            ]);
        }

        Task::deleteRelated($task);

        return response()->json([
            'success' => true,
            'message' => 'The task is deleted successfully along with all it\'s subtasks.'
        ]);
    }

    public function search(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:100'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $tasks = Task::where('title', 'LIKE', 
                        '%'. $request->get('title'). '%')
                        ->get();
        $result = [];

        foreach($tasks as $task){
            $result[] = $this->getTaskData($task);
        }        

        return response()->json([
            'success' => true,
            'message' => 'Retrieved matching tasks successfully.',
            'data' => $result
        ]);
    }

    public function list(Request $request){
        $validator = Validator::make($request->all(), [
            'task_range' => 'nullable|in:today,this week,next week,overdue',
            'pending' => 'required|in:-1,1,0'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        if($request->get('task_range') == 'today'){
            $tasks = Task::where('due_date', date("Y-m-d"));
        }else if($request->get('task_range') == 'this week'){
            $tasks = Task::whereBetween('due_date', [
                        date("Y-m-d", strtotime("this week")),
                        date("Y-m-d", strtotime("next week -1 day")),
                        ]);
        }else if($request->get('task_range') == 'next week'){
            $tasks = Task::whereBetween('due_date', [
                        date("Y-m-d", strtotime("next week")),
                        date("Y-m-d", strtotime("next week +6 days")),
                        ]);
        }else if($request->get('task_range') == 'overdue'){
            $tasks = Task::where('due_date', '<',date("Y-m-d"           ));
        }

        if($request->get('pending') == '1'){
            $tasks->where('task_status', Task::getPendingStatus());
        }else if($request->get('pending') == '0'){
            $tasks->where('task_status', Task::getCompletedStatus());
        }

        $tasks = $tasks->orderBy('due_date')
                        ->get();
        $result = [];

        foreach($tasks as $task){
            $result[] = $this->getTaskData($task);
        }        

        return response()->json([
            'success' => true,
            'message' => 'Task list retrieved successfully.',
            'data' => $result
        ]);
    }

    private function getTaskData(Task $task){
        return [
                'title' => $task->title,
                'due_date' => $task->due_date,
                'isSubtask' => !is_null($task->parent_id),
                'isPending' => $task->isPending(),
                'isCompleted' => $task->isCompleted()
            ];
    }

    public function deleteAll(){
        // soft deleted tasks older than a month
        $onlySoftDeletedRows = Task::select('id')->
                                    onlyTrashed()->
                                    where('deleted_at','<', date("Y-m-d", strtotime("-1 month"))
                                   )->get();

        foreach($onlySoftDeletedRows as $row){
            $row->forceDelete();// permanently delete
        }

        return response()->json([
            'success' => true,
            'message' => 'All tasks soft-deleted for more than a month are deleted!'
        ]);
    }
}
