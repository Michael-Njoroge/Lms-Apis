<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorController extends Controller {
	public function prepareTwoFactor(Request $request) {
		$user = auth()->user();

		$secret = $user->createTwoFactorAuth();
		return response()->json([
			'success' => true,
			'data' => [
				'qr_code' => $secret->toQr(),
				'uri' => $secret->toUri(),
				'string' => $secret->toString(),
			],
			'message' => 'Success.',
		]);
	}

	public function confirmTwoFactor(Request $request) {
		$request->validate([
			'code' => 'required|numeric',
		]);

		$user = auth()->user();

		if ($user->confirmTwoFactorAuth($request->code)) {
			return response()->json([
				'success' => true,
				'recovery_codes' => $user->getRecoveryCodes(),
			]);
		}

		return response()->json([
			'success' => false,
			'message' => 'Invalid 2FA code.',
		], 400);
	}

	public function showRecoveryCodes(Request $request) {
		$user = auth()->user();

		if ($user->hasTwoFactorEnabled()) {
			return response()->json([
				'recovery_codes' => $user->getRecoveryCodes(),
			]);
		}

		return response()->json([
			'success' => false,
			'message' => 'Two-factor authentication is not enabled.',
		], 400);
	}
}
