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

    public function login(Request $r){

        try{

            $creds=$r->validate(['email'=>'required|email','password'=>'required']);

            $user=User::where('email',$r->email)->first();

            if($user && Hash::check($r->password,$user->password)){

                $token=$user->createToken('myapptoken')->plainTextToken;

                return response()->json(['status'=>'success','msg'=>'user logged in successfully','token'=>$token],200);

            }

            return response()->json(['status'=>'success','msg'=>'credentials do not match'],200);

            }catch(Exception $e){

            return response()->json(['msg'=>$e->getMessage()]);
        }

        


    }



    public function logout(Request $r){

        try{

            auth()->user()->tokens()->delete();

            return response()->json(['status'=>'success','message'=>'user logged out successfully'],200);

        }catch(Exception $e){

        }


            


        
    }


    public function register(Request $r){

        try{

            $r->validate(['email'=>'required|email|unique:users','password'=>'required|confirmed']);

            $user=new User;
            $user->name=$r->name;
            $user->password=Hash::make($r->password);
            $user->email=$r->email;
            $user->save();
            // $user->;

            Cache::forget('users');

            return response()->json(['status'=>'success','message'=>'user registered successfully'],201);

        }catch(Exception $e){


            return response()->json(['msg'=>$e->getMessage()]);




        }
        

        }


        public function users(){



            $users=Cache::remember('users',600,function(){

                return User::all();
            });

            if($users->count()>0){

                return response()->json(['status'=>'success','users'=>$users],200);
            }else{

                return response()->json(['status'=>'error','message'=>'there are no users'],404);
            }
        }
}
