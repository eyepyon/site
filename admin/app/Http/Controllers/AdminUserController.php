<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                    ->orWhere('xrpl_address', 'like', "%{$request->search}%");
            });
        }

        $users = $query->withCount(['listings', 'purchases', 'sales'])
            ->latest()
            ->paginate(20);

        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['listings', 'purchases.listing', 'sales.listing']);
        
        return view('users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'ユーザーを削除しました');
    }
}
