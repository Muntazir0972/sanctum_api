<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signup(Request $data){
        
        $validateUser =  Validator::make($data->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation Error',
                'errors' => $validateUser->errors()
            ],401);
        }

        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => $data->password,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User Created successfully',
            'user' => $user,
        ],200);
    }

    public function login(Request $data){

        $validateUser =  Validator::make($data->all(),[
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication Failed',
                'errors' => $validateUser->errors()
            ],404);
        }

        if (Auth::attempt(['email' => $data->email,'password' => $data->password])) {
            $authUser = Auth::user();
            return response()->json([
                'status' => true,
                'message' => 'User Logged in successfully',
                'token' => $authUser->createToken("API Token")->plainTextToken,
                'token_type' => 'bearer'
            ],200);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'Email & Password does not match.',
            ],401);
        }
        
    }

    public function logout(Request $data){

        $user = $data->user();
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'user' => $user,
            'message' => 'You Logged out successfully',
        ],200);
    }
}
