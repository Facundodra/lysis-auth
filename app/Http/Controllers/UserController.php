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
    private const SUCCESS = 1;

    public function Create(Request $request) {
        $validation = $this->validateCreation($request);

        if($validation !== "true")
            return $validation;      

        try {
            return $this->create($request);
        }
        catch (QueryExcpetion $e) {
            return $this->handleError($e);
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
            "birthDate" => "required",
            "subscriptionId" => "required"
        ]);

        if($validator->fails()) 
            return $validator->errors()->toJson();

        return "true";
    }

    private function createUser($request) {
        $client = new Client([
            "surname" => $request->post("surname"),
            "birth_date" => $request->post("birthDate"),
            "subscription_id" => $request->post("subscriptionId")
        ]);

        $user = User::create([
            "name" => $request -> post("name"),
            "email" => $request -> post("email"),
            "password" => Hash::make($request -> post("password"))
        ]);

        $client->save();
        $client->refresh();

        $client->user()->save($user);

        return $client;
    }

    private function handleError($e) {
        return $e->getMessage();
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
            "result" => "Succesful log in."
        ];
    }
}
