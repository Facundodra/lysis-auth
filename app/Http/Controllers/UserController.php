<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use \Illuminate\Database\QueryException;

class UserController extends Controller
{

    /**
     * Creates a new user.
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function Create(Request $request) {
        $validator = Validator::make($request -> all(),[
            'name' => 'required',
            'email' => 'required',
            'password' => 'required'
        ]);

        if($validator -> fails()) 
            return $validator->errors()->toJson();

        try {
            return User::create([
                'name' => $request -> post('name'),
                'email' => $request -> post('email'),
                'password' => Hash::make($request -> post('password'))
            ]);
        }
        catch (QueryExcpetion $e) {
            return [
                'error' => 'User ' . $request -> post('name') . ' already exists.',
                'trace' => $e -> getMessage()
            ];
        }
    }

    /**
     * Authenticates a user.
     * 
     * @param \Illuminate\Http\Request $request
     */

    public function Authenticate(Request $request) {
        $remember = false;

        $validator = Validator::make($request -> all(), [
            'email' => 'required',
            'password' => 'required',
            'remember' => 'required'
        ]);

        if($request -> post('remember') === true) 
            $remember = true;

        if($validator -> fails())
            return $validator -> $errors()->toJson();

        if(!Auth::attempt([
            'email' => $request -> post('email'),
            'password' => $request -> post('password')
        ], $remember)) {
            return [
                'result' => "Credentials don't match."
            ];
        }

        return [
            'result' => 'Log in succesful.'
        ];
    }
}
