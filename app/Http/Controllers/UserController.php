<?php


namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function show($id) {
        return view('user', [
            'user' => User::query()->findOrFail($id)
        ]);
    }

    public function all() {
        return view('user', [
            'user' => User::all()
        ]);
    }

    public function store(Request $request) {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);

        $user->save();

        return view('user', [
            'user' => $user
        ]);
    }
}
