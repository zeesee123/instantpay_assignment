<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Task;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

    protected function getCacheKey($board_id){

        return 'board_'.$board_id.'_tasks';


    }

    public function getUser($board_id){

        $board=Board::find($board_id);

        if(Auth::id()==$board->user_id){

            return true;
        }

        return false;


    }
    /**
     * Display a listing of the resource.
     */
    public function index(string $id)
    {
        //

        try{

            // $board=Board::find($id);

            $board=Board::findorFail($id);

            if($this->getUser($board->id)){

                $tasks=Cache::remember($this->getCacheKey($board->id),600,function(){

                    return $board->tasks()->get();
                });
    
                return response()->json(['status'=>'success','tasks'=>$tasks],200);
            }

            
               return response()->json(['status'=>'error','message'=>'this board is not yours'],404);

            

            


        }catch(Exception $e){

            return response()->json(['status'=>'error','tasks'=>$e->getMessage()],500);
        }
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,string $id)
    {
        //
        try{

            $request->validate(['name'=>'required']);

            $board=Board::findOrFail($id);

            $user=$board->user;

            

            // $boards=$user->boards()->get();//this is the way of using stuff in case of hasMany

            // dd($user);

            // return response()->json(['user'=>$user,'boards'=>$boards]);

            if(Auth::id()==$user->id){

                $task=new Task;
                $task->name=$request->name;
                $task->board_id=$request->board_id;
                $task->save();

                Cache::forget($this->getCacheKey($board->id));

                return response()->json(['status'=>'success','message'=>'task created successfully'],201);

            }else{

                return response()->json(['status'=>'error','message'=>'you are not allowed to add task to this board'],403);
            }

            

        }catch(Exception $e){

            return response()->json(['status'=>'error','message'=>$e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //

        $task=Task::findOrFail($id);

        if($task){

            return response()->json(['status'=>'success','task'=>$task],200);

        }else{

            return response()->json(['status'=>'error','message'=>'there is no such task'],404);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //

        $request->validate(['name'=>'required']);

        $task=Task::find($id);

        if($task){

            if($this->getUser($task->board_id)){

                $task->name=$request->name;
                $task->save();

                Cache::forget($this->getCacheKey($task->board_id));

                return response()->json(['status'=>'success','message'=>'task updated successfully'],200);

            }else{

                return response()->json(['status'=>'error','message'=>'you are not allowed to do that'],403);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $task=Task::find($id);

        if($task){

            if($this->getUser($task->board_id)){

                $task->delete();

                Cache::forget($this->getCacheKey($task->board_id));

                return response()->json(['status'=>'success','message'=>'task successfully deleted']);

            }else{

                return response()->json(['status'=>'error','message'=>'you are not allowed to do that'],403);

            }
        }
    }
}
