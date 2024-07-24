<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function registerUser(Request $request)
    {
        $data = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'mobile' => 'required|string|unique:users,mobile',
            'user_image' => 'nullable|string',
            'profession' => 'required|string',
            'password' => 'required|string',
        ]);

        $rawPassword = $data['password'];
        $data['password'] = Hash::make($rawPassword);

        $user = User::create($data);

        return response()->json([
            'success' => true,
            'data' => 
            [
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'email' => $user->email,
                'mobile' => $user->mobile,
            ],
            'message' => 'User created successfully',
        ]);
        
    }
}
