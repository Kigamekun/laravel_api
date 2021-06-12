<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\auth_token;
class ApiController extends Controller
{
    public function verify_token($token)
    {
        if (Hash::check(Auth::id(), $token)) {
            return true;
        }
        return false;
    }

    //Auth

    public function login(Request $request)
    {
        if (is_null(Auth::id())) {
            $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:255',
            
            'password'=>'required'
        ]);
            if ($validator->fails()) {
                return response()->json([
                "error" => 'validation_error',
                "message" => $validator->errors(),
            ], 422);
            }
        
            if (!Auth::attempt(['name'=>$request->name,'password'=>$request->password])) {
                return response(['message' => 'This User does not exist, check your details , Invalid Login'], 401);
            }
            Auth::attempt(['name' => $request->name, 'password' => $request->password]);

            auth_token::create(['user_id'=>Auth::id(),'token'=>Hash::make(Auth::id())]);
        

       

            return response(['user' => Auth::user(), 'access_token' => auth_token::where('user_id', Auth::id())->first()->token], 200);
        }
        else {
            return response(['message'=>"you has been login ",'user' => Auth::user()], 200);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2|max:255',
            'email' => 'required',
            'password'=>'required|confirmed'
        ]);
        
            if($validator->fails()){
                return response()->json([
                    "error" => 'validation_error',
                    "message" => $validator->errors(),
                ], 422);
            }

        $pass = Hash::make($request->password);

        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>$pass
        ]);

        auth_token::create(['user_id'=>$user->id,'token'=>Hash::make($user->id)]);
        
        Auth::attempt(['email' => $request->email, 'password' => $request->password]);
       

        return response(['user' => Auth::user(), 'access_token' => auth_token::where('user_id',Auth::id())->first()->token],200);
            


    }
    public function logout(Request $request)
    {

        if (app('App\Http\Controllers\API\ApiController')->verify_token($request->auth_token)) {
            auth_token::where(['user_id'=>Auth::id()])->delete();
        Auth::logout();
        return response(['message'=>'you has been logout'],201);
            
        }
        return response(['message'=>'unauthorized user'],401);
        
            

    }



}
