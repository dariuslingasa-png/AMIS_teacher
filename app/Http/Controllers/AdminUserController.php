<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $admins->count(),
            'verified' => $admins->where('account_status', 'verified')->count(),
            'current' => auth()->user(),
        ];

        return view('admin.admins.index', compact('admins', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'username'          => $this->uniqueUsername($request->name),
            'password'          => Hash::make($request->password),
            'role'              => 'admin',
            'account_status'    => 'verified',
            'email_verified_at' => now(),
        ]);

        return back()->with('success', "Admin account created for {$request->name}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        if ($user->role !== 'admin') {
            return back()->withErrors(['error' => 'User is not an admin.']);
        }

        $user->delete();
        return back()->with('success', "Admin account removed.");
    }

    private function uniqueUsername(string $name): string
    {
        $base = Str::of($name)->lower()->slug('.')->value() ?: 'admin';
        $username = $base;
        $counter = 2;

        while (User::where('username', $username)->exists()) {
            $username = "{$base}.{$counter}";
            $counter++;
        }

        return $username;
    }
}
