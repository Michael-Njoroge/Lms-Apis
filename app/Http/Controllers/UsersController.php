<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UsersResource;
use App\Mail\ResetPassword;
use Illuminate\Support\Facades\Mail;
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
        $createdUser = User::findOrFail($user->id);

        return $this->sendResponse(UsersResource::make($createdUser)
            ->response()
            ->getData(true),'User created successfully');
        
    }

    public function loginUser(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user && Hash::check($data['password'], $user->password)) {
            return $this->sendError("Invalid Credentials");
        }

        $token = $user->createToken("access_token")->plainTextToken;

        return response()->json([
            'success' => true,
            'data' => 
            [
                'id' => $user->id,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'token' => $token,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'role' => $user->role,
                'username' => strtolower($user->firstname . $user->lastname),
                'profile' => $user->user_image,
            ],
            'message' => 'Logged in successfully',
        ]);

    }

    public function getUsers()
    {
        $users = User::paginate(20);
        return $this->sendResponse(UsersResource::collection($users)
            ->response()
            ->getData(true),'Users retrieved successfully');
    }

    public function getUser(User $user)
    {
        return $this->sendResponse(UsersResource::make($user)
            ->response()
            ->getData(true),'User retrieved successfully');
    }

    public function updateUser(Request $request ,User $user)
    {
        $user->update($request->all());
        $updatedUser = User::findOrFail($user->id);
        return $this->sendResponse(UsersResource::make($updatedUser)
            ->response()
            ->getData(true),'User updated successfully');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return $this->sendResponse([],'User deleted successfully');
    }

    public function blockUnblockUser(Request $request ,User $user)
    {
        $user->is_blocked = !$user->is_blocked;
        $user->save();

       $message = 'User was ' . ($user->is_blocked ? 'Blocked' : 'Unblocked');
        return $this->sendResponse(UsersResource::make($user)
            ->response()
            ->getData(true), $message);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'currentPassword' => 'required|string',
            'password' => 'required|confirmed|string|min:6',
        ]);

        $user = auth()->user();
        if(!Hash::check($data['currentPassword'], $user->password)){
            return $this->sendError('Your current password is incorrect');
        }
        if(Hash::check($data['password'], $user->password)){
            return $this->sendError('The new password cannot be the same as the current password.');
        }

        $rawPassword = $data['password'];
        $user->password = Hash::make($rawPassword);
        $user->save();

        return $this->sendResponse(UsersResource::make($user)
            ->response()
            ->getData(true),'Password updated successfully');
        
    }

    public function forgotPassword(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email'
        ]);

        $user = User::where('email', $data['email'])->first();
        $token = $user->createResetPasswordToken();
        $resetUrl = env('BASE_URL') . '/reset-password?email='.$user->email.'&token='.$token;
         Mail::to($user->email)->send(new ResetPassword($user,$resetUrl));

        return $this->sendResponse([], "Please check your mail, we have sent a password reset link valid for the next 10 minutes" );
    }
}
   