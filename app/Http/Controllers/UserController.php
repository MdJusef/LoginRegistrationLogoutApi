<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Validator;
use Illuminate\Support\Facades\Hash;



class UserController extends Controller
{
    // Register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);
        return response()->json([
            'message' => 'User registration Successfully',
            'user' => $user
        ]);
    }
    // Login

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required|string|min:6'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        if (!$token = auth()->attempt($validator->validated()))
        {
            return response() ->json(['error'=>'Unauthorized']);
        }
        return $this->responseWithToken($token);
    }
    protected function responseWithToken($token){
        return response()->json([
            'access_token'=>$token,
            'token_type'=>'bearer',
            'expires_in'=>auth()->factory()-> getTTL() * 60
        ]);
    }

    // Profile
    public function profile()
    {
        return response()->json(auth()->user());
    }

    //Refresh
    public function refresh()
    {
        return $this->responseWithToken(auth()->refresh());
    }

    //logout
    public function logout()
    {
        auth()->logout();
        return response()->json(['message'=>'User Successfully Logged Out']);
    }

}
