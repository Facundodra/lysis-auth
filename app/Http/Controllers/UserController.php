<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Client;

class UserController extends Controller
{
    public function create(CreateUserRequest $request) {
        try {
            DB::transaction(function () use($request, $user, $client) {
                Client::create([
                    'surname' => $request->post('surname'),
                    'birth_date' => $request->post('birthDate')
                ]);
                User::create([
                    'name' => $request -> post('name'),
                    'email' => $request -> post('email'),
                    'password' => Hash::make($request -> post('password'))
                ]);
                $client->user()->save($user);
            });
        }
        catch (QueryException $e) {
            return [
                'result' => 'Unable to create user right now.'
            ];
        }

        return [
            'result' => 'User registered succesfully.'
        ];
    }
    
    public function authenticate(Request $request) {
        $validation = $this->validateAuthentication($request);
        if($validation !== true)
            return $validation;

        if(!Auth::attempt($request->only('email', 'password'), $request->post('remember')))
            return [
                'result' => 'Wrong password or email.'
            ];

        return [
            'result' => 'Succesfully logged in.',
            'subscriptionId' => User::firstWhere('id', Auth::id())->client->subscription_id,
            'subscription' => Auth::user()->client->subscription->type
        ];
    }

    public function update(UpdateUserRequest $request)
    {
        try {
            DB::transaction(function () use ($request){
                User::find(Auth::id())->update([
                    'name' => $request->post('name'),
                    'email' => $request->post('email'),
                ]);
                if(!empty($request->post('password')))
                    User::find(Auth::id())->update([
                        'password' => Hash::make($request->post('password'))
                    ]);
                User::find(Auth::id())->client->update([
                    'surname' => $request->post('surname'),
                    'birth_date' => $request->post('birthdate')
                ]);
        });
        } catch (QueryExcpetion $e) {
            return [
                'result' => 'Unable to update user right now.'
            ];
        }

        return [
            'result' => 'User information updated succesfully.'
        ];
    }

    private function validateAuthentication($request) {
        $validator = Validator::make($request -> all(), [
            'email' => 'required|email|exists:users',
            'password' => 'required',
            'remember' => 'required|boolean'
        ]);
        if($validator -> fails())
            return $validator->errors()->toJson();

        return true;
    }
}
