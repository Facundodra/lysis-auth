<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class UserController extends Controller
{

    public function Create(Request $request) {
        $validation = $this->validateCreation($request);

        if($validation !== true)
            return $validation;      

        try {
            return $this->createUser($request);
        }
        catch (QueryException $e) {
            return $e->getMessage();
        }
    }
    
    public function Authenticate(Request $request) {
        $remember = false;

        if($request -> post("remember") === true) 
            $remember = true;

        return $this->doAuthentication([
            "email" => $request -> post("email"),
            "password" => $request -> post("password")
        ],
            $remember
        );
    }

    private function validateCreation($request) {
        $validator = Validator::make($request -> all(),[
            "name" => "required",
            "email" => "required",
            "password" => "required",
            "surname" => "required",
            "birthDate" => "required"
        ]);

        if($validator->fails()) 
            return $validator->errors()->toJson();

        return true;
    }

    private function createUser($request) {
        $client = Client::create([
            "surname" => $request->post("surname"),
            "birth_date" => $request->post("birthDate")
        ]);

        $user = new User([
            "name" => $request -> post("name"),
            "email" => $request -> post("email"),
            "password" => Hash::make($request -> post("password"))
        ]);

        $client->user()->save($user);

        return [
            "result" => "Registered succesfully."
        ];
    }

    private function validateAuthentication($request) {
        $validator = Validator::make($request -> all(), [
            "email" => "required",
            "password" => "required",
            "remember" => "required"
        ]);

        if($validator -> fails())
            return $validator -> $errors()->toJson();

        return $validator;
    }

    private function doAuthentication($credentials, $remember) {
        if(!Auth::attempt($credentials, $remember)) {
            return [
                "result" => "Credentials don't match any registered user."
            ];
        }
        return [
            "subscription" => Auth::user()->subscriptionType()->first(),
            "subscriptionId" => Auth::user()->client->subscription_id
        ];
    }
}
