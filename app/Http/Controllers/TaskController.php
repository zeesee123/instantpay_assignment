<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Task;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{

    /**
     * Generate a cache key for storing tasks related to a specific board.
     *
     * @param int $board_id
     * @return string
     */

    protected function getCacheKey($board_id){

        return 'board_'.$board_id.'_tasks';


    }


    /**
     * Check if the authenticated user is the owner of the board.
     *
     * @param int $board_id
     * @return bool
     */

    public function isBoardOwner($board_id){

        $board=Board::find($board_id);

        if(Auth::id()==$board->user_id){

            return true;
        }

        return false;
      
     }



    /**
     * Display a listing of the tasks for the specified board.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function index(string $id)
    {
        

        try{

            // Retrieve the board, or fail if it doesn't exist
            $board=Board::findOrFail($id);

            if($this->isBoardOwner($board->id)){

                $tasks=Cache::remember($this->getCacheKey($board->id),600,function() use ($board){

                    return $board->tasks()->get();
                });

                if($tasks->isEmpty()){
                
                    return response()->json(['status'=>'error','message'=>'Tasks not found'],404);
                }
    
                return response()->json(['status'=>'success','tasks'=>$tasks],200);
            }

            
               return response()->json(['status'=>'error','message'=>'Unauthorized access to this board'],403);

            

          }catch(Exception $e){

            return response()->json(['status'=>'error','tasks'=>'Failed to retrieve tasks: '.$e->getMessage()],500);
        }
        
    }

/**
     * Store a newly created task in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request,string $id)
    {
        //
        try{

            // Validate the request data
            $request->validate(['name'=>'required']);


            $board=Board::findOrFail($id);

            $user=$board->user;

            
            // Check if the current user is the owner of the board
            if(Auth::id()==$user->id){

                $task=new Task;
                $task->name=$request->name;
                $task->board_id=$id;
                $task->save();

                // Clear the cache for the board's tasks
                Cache::forget($this->getCacheKey($board->id));

                return response()->json(['status'=>'success','message'=>'Task created successfully'],201);

            }else{

                return response()->json(['status'=>'error','message'=>'Unauthorized to add task to this board'],403);
            }

            

        }catch(Exception $e){

            return response()->json(['status'=>'error','message'=>'Failed to create the task: '.$e->getMessage()]);
        }
    }


    /**
     * Display the specified task.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function show(string $id)
    {
        //

        try{

            $task=Task::findOrFail($id);

            $board=Board::findOrFail($task->board_id);


            if(!$this->isBoardOwner($board->id)){

                return response()->json(['status'=>'error','task'=>'Unauthorized to view the task'],403);

            }

            return response()->json(['status'=>'success','task'=>$task],200);


        }catch(Exception $e){

            return response()->json(['status'=>'error','message'=>'Failed to retrieve the task: '.$e->getMessage()],500);
        }

        
    }


      /**
     * Update the specified task in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function update(Request $request, string $id)
    {
        //


        try{

            $request->validate(['name'=>'required']);

            $task=Task::findOrFail($id);

            
            // Check if the current user is the owner of the board
            if($this->isBoardOwner($task->board_id)){

                    $task->name=$request->name;
                    $task->save();

                    Cache::forget($this->getCacheKey($task->board_id));

                    return response()->json(['status'=>'success','message'=>'Task updated successfully'],200);

            }else{

                    return response()->json(['status'=>'error','message'=>'Unauthorized to update the task'],403);
            }


        }catch(Exception $e){

            return response()->json(['status'=>'error','message'=>'Failed to update the task: '.$e->getMessage()],500);
        }

        
        
    }


    /**
     * Remove the specified task from storage.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroy(string $id)
    {
        //

        try{

            $task=Task::findOrFail($id);

        
            // Check if the current user is the owner of the board
            if($this->isBoardOwner($task->board_id)){

                $task->delete();

                // Clear the cache for the board's tasks
                Cache::forget($this->getCacheKey($task->board_id));

                return response()->json(['status'=>'success','message'=>'Task deleted successfully']);

            }else{

                return response()->json(['status'=>'error','message'=>'Unauthorized to delete this task'],403);

            }


        }catch(Exception $e){

            return response()->json(['status'=>'error','message'=>'Failed to delete task: '.$e->getMessage()],500);
        }

        
        
    }
}
