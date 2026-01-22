<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required'
        ]);

        $adminPassword = env('ADMIN_PASSWORD');

        if (!$adminPassword) {
            abort(500, 'Admin password not set');
        }

        if (!Hash::check($request->password, bcrypt($adminPassword))) {
            return back()->with('error', 'Password admin salah.');
        }

        session(['is_admin' => true]);

        return redirect()->route('admin.dashboard');
    }

    public function logout()
    {
        session()->forget('is_admin');
        return redirect()->route('admin.login');
    }
}
