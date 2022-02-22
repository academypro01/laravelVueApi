<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserLoginResource;
use App\Http\Resources\UserRegisterResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $data = $this->validate($request, [
            'name' => ['required'],
            'email' => ['required', 'unique:users'],
            'password' => ['required', 'min:8']
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->createToken('accessToken');

        return new UserRegisterResource($user);
    }

    public function login(Request $request)
    {
        $data = $this->validate($request, [
            'email' => ['required','exists:users'],
            'password' => ['required']
        ]);

        Auth::attempt($data);

        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'token' => $user->createToken('accessToken')->plainTextToken
            ]
        ]);
    }
}
