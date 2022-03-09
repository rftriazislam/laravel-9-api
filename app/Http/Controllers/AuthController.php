<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $validatedData =   $this->validate($request, [
            'name' => 'required|min:4',
            'email' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);
        $validatedData['password'] = bcrypt($request->password);
        $user = User::create($validatedData);
        $token = $user->createToken('token')->plainTextToken;
        return response(['success'=>true,'user' =>$user, 'token' => $token],201);
    }

    public function login(Request $request) {
        $loginData = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if (!auth()->attempt($loginData)) {
            return response(['user' => null, 'message' => 'Email or Password Invalied']);
        }

        $token = auth()->user()->createToken('token')->plainTextToken;
        return response(['success'=>true,'user' =>auth()->user(), 'token' => $token],201);

    }

    public function logout() {
        auth()->user()->tokens()->delete();
        return response(['success'=>true,'message' => 'Successfull Logout'],200);
    }

    public function users()
    {
     $users=User::all();
     return response(['success'=>true,'users' => $users],200);

    }
}
