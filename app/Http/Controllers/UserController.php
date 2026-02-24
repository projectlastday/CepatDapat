<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Authorization: Only Admin (1) and Super Admin (7)
        $userType = session('id_user_type');
        if (!in_array($userType, [1, 7])) {
            return redirect('/')->with('error', 'Akses ditolak.');
        }

        $search = $request->input('search', '');

        $query = DB::table('users')
            ->where('id_user_type', '!=', 7) // Never show Super Admin data
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('telepon', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users', [
            'users' => $users,
            'search' => $search,
        ]);
    }
}
