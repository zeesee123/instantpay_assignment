<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    //

    /**
     * Handle user login.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $r){

        try{

            // Validate request data
            $creds=$r->validate(['email'=>'required|email','password'=>'required']);

            $user=User::where('email',$r->email)->first();

            if($user && Hash::check($r->password,$user->password)){

                // Generate token for authenticated user
                $token=$user->createToken('myapptoken')->plainTextToken;

                return response()->json(['status'=>'success','msg'=>'User logged in successfully','token'=>$token],200);

            }

            return response()->json(['status'=>'success','msg'=>'Credentials do not match'],200);

            }catch(Exception $e){

            return response()->json(['status'=>'error','message'=>'Failed to login the user: '.$e->getMessage()],500);
        }

        


    }


     /**
     * Handle user logout.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout(Request $r){

        try{

            // Delete all tokens for the authenticated user
            auth()->user()->tokens()->delete();

            return response()->json(['status'=>'success','message'=>'User logged out successfully'],200);

        }catch(Exception $e){

            return response()->json(['status'=>'error','message'=>'Failed to logout the user: '],500);

        }


    }


    /**
     * Handle user registration.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function register(Request $r){

        try{

            $r->validate(['email'=>'required|email|unique:users','password'=>'required|confirmed']);

            $user=new User;
            $user->name=$r->name;
            $user->password=Hash::make($r->password);
            $user->email=$r->email;
            $user->save();
            // $user->;

            // Clear user cache
            Cache::forget('users');

            return response()->json(['status'=>'success','message'=>'User registered successfully'],201);

        }catch(Exception $e){


            return response()->json(['status'=>'error','message'=>'Failed to register the user: '.$e->getMessage()]);




        }
        

        }


        /**
        * Get a list of users.
        *
        * @return \Illuminate\Http\JsonResponse
        */

        public function users(){

            try{

                $users=Cache::remember('users',600,function(){

                    return User::all();
                });
    
                if($users->isEmpty()){
    
                    return response()->json(['status'=>'error','message'=>'No users found'],404);
                    
                }
    
                return response()->json(['status'=>'success','users'=>$users],200);


            }catch(Exception $e){

                return response()->json(['status'=>'error','message'=>'Failed to retrieve the users: '.$e->getMessage()],500);
            }


            
        }
}
