<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

        return view('account-settings', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'display_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
        ]);

        // ユーザー情報を更新
        if ($request->filled('display_name')) {
            $user->display_name = $request->display_name;
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        $user->save();

        return redirect()
            ->route('account.settings')
            ->with('success', 'アカウント情報を更新しました。');
    }
}
