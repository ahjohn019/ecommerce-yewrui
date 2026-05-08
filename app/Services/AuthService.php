<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService extends ResponseService
{
    public function register(array $data): array
    {
        $user = User::create($data);
        $token = $user->createToken('auth_token')->plainTextToken;

        return compact('user', 'token');
    }

    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return compact('user', 'token');
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }
}
