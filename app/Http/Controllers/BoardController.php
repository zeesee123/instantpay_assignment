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
     * Display a listing of the resource.
     */

     protected function getCacheKey(){

        return 'board_'.Auth::id();
     }

     public function user($board){

        if($board->user_id==Auth::id()){

            return true;
        }

        return false;
     }

    
    public function index()
    {
        //

        try{

            

            $boards=Cache::remember($this->getCacheKey(),600,function(){
                
                return Board::where('user_id',Auth::id())->get();
            });

            if($boards->count()>0){

               return response()->json(['status'=>'success','boards'=>$boards],200);

            }else{

               return response()->json(['status'=>'error','message'=>'there are no boards'],404);

            }

        }catch(Exception $e){
        
            return response()->json(['status'=>'error','message'=>$e->getMessage()],500);

        }

        
    }

    /**
     * Store a newly created resource in storage.
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

            Cache::forget($this->getCacheKey());


            return response()->json(['status'=>'success','message'=>'board created successfully','board'=>$board]);



            
        }catch(Exception $e){

            return response()->json(['status'=>'success','message'=>'board created successfully']);


        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $board=Board::find($id);

        if($board){

            return response()->json(['status'=>'success','board'=>$board],200);
        
        }else{

            return response()->json(['status'=>'error','message'=>'there is no such board'],404);

        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //

        try{

            

            $request->validate(['name'=>'required']);

            $board=Board::find($id);

            if($this->user($board)){

                if($board){

                    $board->name=$request->name;
                    $board->save();

                    Cache::forget($this->getCacheKey());

                    return response()->json(['status'=>'success','message'=>'board updated successfully','board'=>$board],200);

                 }else{
     
                    return response()->json(['status'=>'error','message'=>'the board does not exist'],404);
                 }

            }else{
                
                return response()->json(['status'=>'error','message'=>'you are not allowed to update this board']);
            }

            

        }catch(Exception $e){

            return response()->json(['status'=>'error','message'=>$e->getMessage()]);

        }
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        try{

            $board=Board::find($id);

            if($board){

                if($this->user($board)){


                    $board->delete();

                    Cache::forget($this->getCacheKey());

                    return response()->json(['status'=>'success','message'=>'board destroyed successfully'],200);
                
                }else{

                    return response()->json(['status'=>'success','message'=>'you cannot do this'],200);

                }
            }

            return response()->json(['status'=>'error','message'=>'board does not exist'],404);

        }catch(Exception $e){

            return response()->json(['status'=>'error','message'=>$e->getMessage()],500);
        }
    }
}
