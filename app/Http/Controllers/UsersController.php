<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoginHistoryResource;
use App\Http\Resources\UsersResource;
use App\Mail\ResetPassword;
use App\Models\LoginHistory;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laragear\TwoFactor\Facades\Auth2FA;

class UsersController extends Controller {
	public function registerUser(Request $request) {
		$data = $request->validate([
			'firstname' => 'required|string',
			'lastname' => 'required|string',
			'email' => 'required|email|unique:users,email',
			'mobile' => 'required|string|unique:users,mobile',
			'user_image' => 'nullable|string',
			'profession' => 'required|string',
			'password' => 'required|string',
		]);

		$data['status'] = 'pending';
		$rawPassword = $data['password'];
		$data['password'] = Hash::make($rawPassword);

		$user = User::create($data);
		event(new Registered($user));
		$createdUser = User::findOrFail($user->id);

		return $this->sendResponse(UsersResource::make($createdUser)
				->response()
				->getData(true), 'Please verify your email.');

	}

	public function loginUser(Request $request) {
		$data = $request->validate([
			'email' => 'required|email|exists:users,email',
			'password' => 'required|string',
			'remember' => 'boolean',
			'2fa_code' => 'sometimes|required|string',
			'verification_code' => 'sometimes|required|string',
		]);

		$user = User::where('email', $data['email'])->first();

		if (!$user || !Hash::check($data['password'], $user->password)) {
			return $this->sendError("Invalid Credentials");
		}

		// Send the verification code unconditionally
		if (!$request->filled('verification_code')) {
			$user->sendVerificationCode();

			return response()->json([
				'success' => false,
				'message' => 'Verification code sent.',
			], 403);
		}

		// Validate the verification code
		if (!$user->validateVerificationCode($data['verification_code'])) {
			return response()->json(['success' => false, 'message' => 'Invalid verification code.'], 401);
		}

		// Handle two-factor authentication
		if ($user->hasTwoFactorEnabled()) {
			if (!$request->filled('2fa_code')) {
				return response()->json([
					'success' => false,
					'message' => 'Two-factor authentication code is required.',
				], 403);
			}

			if (!Auth2FA::attempt($user, $data['2fa_code'])) {
				return response()->json(['success' => false, 'message' => 'Invalid two-factor authentication code.'], 401);
			}
		}

		// Log the user in
		Auth::login($user, $data['remember'] ?? false);

		// Record login history
		LoginHistory::create([
			'user_id' => $user->id,
			'ip_address' => $request->ip(),
			'user_agent' => $request->header('User-Agent'),
			'login_at' => now(),
		]);

		// Limit the login history to the latest 10 entries
		$loginCount = LoginHistory::where('user_id', $user->id)->count();

		if ($loginCount > 10) {
			LoginHistory::where('user_id', $user->id)
				->orderBy('login_at', 'asc')
				->take($loginCount - 10)
				->delete();
		}

		// Generate access token
		$token = $user->createToken("access_token")->plainTextToken;

		return response()->json([
			'success' => true,
			'data' => [
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
			'message' => 'Welcome back!',
		]);
	}

	public function getLoginHistory() {
		$userId = auth()->id();
		$loginHistory = LoginHistory::with('user')->where('user_id', $userId)->orderBy('login_at', 'desc')->get();
		return $this->sendResponse(LoginHistoryResource::collection($loginHistory)
				->response()
				->getData(true), 'Login history retrieved successfully');
	}

	public function logOut() {
		$user = auth()->user();
		$user->tokens()->delete();
		return $this->sendResponse([], 'Logged out successfully');
	}

	public function getUsers() {
		$users = User::paginate(20);
		return $this->sendResponse(UsersResource::collection($users)
				->response()
				->getData(true), 'Users retrieved successfully');
	}

	public function getUser(User $user) {
		return $this->sendResponse(UsersResource::make($user)
				->response()
				->getData(true), 'User retrieved successfully');
	}

	public function updateUser(Request $request, User $user) {
		$user->update($request->all());
		$updatedUser = User::findOrFail($user->id);
		return $this->sendResponse(UsersResource::make($updatedUser)
				->response()
				->getData(true), 'User updated successfully');
	}

	public function deleteUser(User $user) {
		$user->delete();
		return $this->sendResponse([], 'User deleted successfully');
	}

	public function changeUserStatus(Request $request, User $user) {
		$data = $request->validate([
			'status' => 'required|in:active,inactive,banned',
		]);

		$user->status = $data['status'];
		$user->save();

		return $this->sendResponse(UsersResource::make($user)
				->response()
				->getData(true), "User status updated to {$user->status}");
	}

	public function updatePassword(Request $request) {
		$data = $request->validate([
			'currentPassword' => 'required|string',
			'password' => 'required|confirmed|string',
		]);

		$user = auth()->user();
		if (!Hash::check($data['currentPassword'], $user->password)) {
			return $this->sendError('Your current password is incorrect');
		}
		if (Hash::check($data['password'], $user->password)) {
			return $this->sendError('The new password cannot be the same as the current password.');
		}

		$rawPassword = $data['password'];
		$user->password = Hash::make($rawPassword);
		$user->save();

		return $this->sendResponse(UsersResource::make($user)
				->response()
				->getData(true), 'Password updated successfully');

	}

	public function forgotPassword(Request $request) {
		$data = $request->validate([
			'email' => 'required|email|exists:users,email',
		]);

		$user = User::where('email', $data['email'])->first();
		$token = $user->createResetPasswordToken();
		$resetUrl = env('BASE_URL') . '/reset-password?email=' . $user->email . '&token=' . $token;
		Mail::to($user->email)->send(new ResetPassword($user, $resetUrl));

		return $this->sendResponse([], "We have emailed you a password reset link valid for the next 10 minutes");
	}

	public function resetPassword(Request $request) {
		try {
			$data = $request->validate([
				'email' => 'required|email',
				'token' => 'required',
				'password' => 'required|confirmed',
			]);

			$passwordReset = PasswordReset::where('email', $data['email'])->first();

			if ($passwordReset && Hash::check($data['token'], $passwordReset->token)) {
				$createdAt = new Carbon($passwordReset->created_at);

				if ($createdAt->addMinutes(10) >= now()) {
					$user = User::where('email', $data['email'])->first();
					$user->password = Hash::make($data['password']);
					$user->save();

					// Delete the token after successful password reset
					$passwordReset->delete();

					return $this->sendResponse([], "Success, use the new password in your next login");
				} else {
					return $this->sendError("The reset token is invalid or has expired, please try again");
				}
			} else {
				return $this->sendError("The reset token is invalid or has expired, please try again");
			}
		} catch (Exception $error) {
			return $this->sendError($error->getMessage(), 500);
		}
	}

	public function assignRole(Request $request, User $user) {
		$data = $request->validate([
			'role_id' => 'required|uuid|exists:roles,id',
		]);

		$user->role_id = $data['role_id'];
		$user->save();

		return $this->sendResponse(UsersResource::make($user)
				->response()
				->getData(true), 'Role assigned successfully');
	}

	public function removeRole(User $user) {
		$user->role_id = null;
		$user->save();

		return $this->sendResponse(UsersResource::make($user)
				->response()
				->getData(true), 'Role removed successfully');
	}
}
