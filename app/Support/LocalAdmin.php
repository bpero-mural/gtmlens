<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LocalAdmin
{
    public static function ensure(): User
    {
        return User::query()->updateOrCreate([
            'email' => config('app.local_admin.email'),
        ], [
            'name' => config('app.local_admin.name'),
            'password' => Hash::make(config('app.local_admin.password')),
            'role' => 'admin',
        ]);
    }
}
