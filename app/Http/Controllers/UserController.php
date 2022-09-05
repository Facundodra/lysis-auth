<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class UserController extends Controller
{
    private const SUCCESS = 1;

    public function Create(Request $request) {
        $validation = validateCreation($request);

        if($validation !== $SUCCESS)
            return $validation;      

        try {
            return create($request);
        }
        catch (QueryExcpetion $e) {
            return handleError($e);
        }
    }
    
    public function Authenticate(Request $request) {
        $remember = false;

        if($request -> post("remember") === true) 
            $remember = true;

        return doAuthentication($request);
    }

    private function validateCreation($request) {
        $validator = Validator::make($request -> all(),[
            "name" => "required",
            "email" => "required",
            "password" => "required"
        ]);

        if($validator -> fails()) 
            return $validator->errors()->toJson();

        return $SUCCESS;
    }

    private function createUser($request) {
        return User::create([
            "name" => $request -> post("name"),
            "email" => $request -> post("email"),
            "password" => Hash::make($request -> post("password"))
        ]);
    }

    private function handleError($e) {
        return $e -> getMessage();
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

    public function doAuthentication($credentials) {
        if(!Auth::attempt($credentials)) {
            return [
                "result" => "Credentials don't match any registered user."
            ];
        }

        return [
            "result" => "Succesful log in."
        ];
    }
}
