<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class SuperAdminController extends Controller
{
    public function login()
    {
        return view('super_admin.login');
    }

    public function authenticate(Request $request)
    {
        $username = $request->input('username');
        $password = $request->input('password');

        if ($username === 'admin' && $password === env('SUPER_ADMIN_PASSWORD')) {
            Session::put('super_admin_authenticated', true);
            return redirect()->route('super_admin.index');
        }

        return back()->withErrors(['message' => 'Invalid credentials']);
    }

    public function logout()
    {
        Session::forget('super_admin_authenticated');
        return redirect()->route('super_admin.login');
    }

    public function index()
    {
        if (!Session::get('super_admin_authenticated')) {
            return redirect()->route('super_admin.login');
        }

        $users = User::where('role', 'barber')->get();
        return view('super_admin.index', compact('users'));
    }

    public function updateExpiry(Request $request, User $user)
    {
        if (!Session::get('super_admin_authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'trial_ends_at' => 'required|date'
        ]);

        $user->update([
            'trial_ends_at' => $request->trial_ends_at
        ]);

        return back()->with('success', "Expiry date updated for {$user->name}");
    }
}
