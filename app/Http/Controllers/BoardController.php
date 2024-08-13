<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BoardController extends Controller
{
    /**
     * Get the cache key for the user's boards.
     *
     * @return string
     */

     protected function getCacheKey(){

        return 'board_'.Auth::id();
     }


    /**
     * Check if the authenticated user is the owner of the given board.
     *
     * @param Board $board
     * @return bool
     */

     public function isBoardOwner($board){

        return $board->user_id === Auth::id();
     }


    /**
     * Display a listing of the boards for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
     public function index()
    {
        

        try{

            // Retrieve boards from cache or query the database if not cached
            $boards=Cache::remember($this->getCacheKey(),600,function(){
                
                return Board::where('user_id',Auth::id())->get();
            });

            if($boards->isEmpty()){

                return response()->json(['status'=>'error','message'=>'No boards found'],404);

            }

            return response()->json(['status'=>'success','boards'=>$boards],200);

        }catch(Exception $e){
        
            return response()->json(['status'=>'error','message'=>'Failed to retrieve boards: '.$e->getMessage()],500);

        }

        
    }

    /**
     * Store a newly created board in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function store(Request $request)
    {
        //

        try{

            

            $request->validate(['name'=>'required']);

            $board=new Board;
            $board->name=$request->name;
            $board->user_id=Auth::id();
            $board->save();


            // Clear cache after creating a new board
            Cache::forget($this->getCacheKey());


            return response()->json(['status'=>'success','message'=>'Board created successfully','board'=>$board],201);



            
        }catch(Exception $e){

            return response()->json(['status'=>'success','message'=>'Failed to create the board: '.$e->getMessage()],500);


        }
    }

    
    /**
     * Display the specified board.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function show(string $id)
    {
        //
        try{

            $board=Board::findOrFail($id);

            // Check if the current user is the owner of the board
            if(!$this->isBoardOwner($board)){

                return response()->json(['status'=>'error','board'=>'Unauthorized access to this board'],403);

            }

            return response()->json(['status'=>'success','board'=>$board],200);

        }catch(Exception $e){

            return response()->json(['status'=>'error','message'=>'Failed to retrieve the board: '.$e->getMessage()],500);
        }
        
        
        
        
    }


    /**
     * Update the specified board in storage.
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

            $board=Board::findOrFail($id);

            // Check if the current user is the owner of the board
            if(!$this->isBoardOwner($board)){

                return response()->json(['status'=>'error','message'=>'Unauthorized to update this board'],403);

            }


            $board->name=$request->name;
            $board->save();

            // Clear cache after updating the board
            Cache::forget($this->getCacheKey());

            return response()->json(['status'=>'success','message'=>'Board updated successfully','board'=>$board],200);

            

        }catch(Exception $e){

            return response()->json(['status'=>'error','message'=>'Failed to update the board: '.$e->getMessage()],500);

        }
        
    }

    /**
     * Remove the specified board from storage.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */

    public function destroy(string $id)
    {
        //
        try{

            $board=Board::findOrFail($id);

            
            // Check if the current user is the owner of the board
            if(!$this->isBoardOwner($board)){

                return response()->json(['status'=>'error','message'=>'Unauthorized to delete this board'],403);
            }


            $board->delete();

            // Clear cache after deleting the board
            Cache::forget($this->getCacheKey());

            return response()->json(['status'=>'success','message'=>'Board deleted successfully'],200);
            

            

        }catch(Exception $e){

            return response()->json(['status'=>'error','message'=>'Failed to delete the board: '.$e->getMessage()],500);
        }
    }
}
