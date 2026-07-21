<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function switchRole(Request $request)
    {
        $role = $request->input('role', 'admin');
        session(['user_role' => $role]);

        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('merchant.dashboard');
        }
    }
}
