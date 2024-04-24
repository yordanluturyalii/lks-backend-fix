<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->only(['email', 'password']), [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:5']
        ]);

        try {
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            if (!Auth::attempt($validator->validated())) {
                return response()->json([
                    'message' => 'Email or password incorrect'
                ], 401);
            }

            $user = Auth::user();
            $token = $user->createToken($request->email)->plainTextToken;
            return response()->json([
                'message' => 'Login Success',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'accessToken' => $token
                ]
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Invalid field',
                'errors' => $e->errors()
            ], 422);
        }
    }

    public function logout() {
        $user = Auth::user();
        if ($user) {
            $user->tokens()->delete();

            return response()->json([
                'message' => 'Logout Success'
            ], 200);
        }
    }
}
