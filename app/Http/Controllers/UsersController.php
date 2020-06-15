<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('roles:admin')->except('update');
    }

    // store
    public function store(Request $request)
    {
        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->role = 'user';
        $user->business_id = $request->input('business_id');
        $user->save();

        // redirect
    }

    // update
    public function update(Request $request)
    {
        try {

            // admin can update any - user only update their own
            if (Auth::user()->role = 'admin') {
                $user = User::findOrFail($request->input('id'));
            } else {
                $user = User::findOrFail(Auth::user()->id);
            }

            if ($request->input('password') != null) {
                $user->password = Hash::make($request->input('password'));
            }

            $user->name = $request->input('name');
            $user->email = $request->input('email');
            $user->phone = $request->input('phone');

            // only admin can update business id
            if (Auth::user()->role = 'admin') {
                $user->business_id = $request->input('business_id');
            }

            $user->save();
            // redirect

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            return Redirect::back()->withErrors('Unable to find user to update');
        }
    }

    // destroy
    public function destroy(User $user)
    {
        $user->delete();
        // redirect
    }

    // validate request
    protected function validateRequest()
    {
        return request()->validate([
            'id' => '',
            'name' => '',
            'email' => '',
            'password' => '',
            'phone' => '',
            'business_id' => 'required',
        ]);
    }
}
