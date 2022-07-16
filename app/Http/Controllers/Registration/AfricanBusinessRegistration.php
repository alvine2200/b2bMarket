<?php

namespace App\Http\Controllers\Registration;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AfricanBusinessRegistration extends BaseController
{
    protected $businessType = "African";
    //
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'business_role_id' => 'required',
            'gender_id' => 'required',
            'email' => 'required|email|unique:users',
            'secondary_email' => 'email|unique:users',
            'phone' => 'required|min:10|unique:users|regex:/^([0-9\s\-\+\(\)]*)$/',
            'secondary_phone' => 'min:10|unique:users|regex:/^([0-9\s\-\+\(\)]*)$/',
            'sector_id' => 'required',
            'location' => 'required|array',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);       
        }
   
        return DB::transaction(function() use ($request){
            $input = $request->all();
            $input['password'] = bcrypt($input['password']);
            $input["business_type"] = $this->businessType;
            $readyInput = collect($input)->except(['location'])->all();
            $user = User::create($readyInput);
    
            # assign location
            $user->locations()->sync($input['location']);
       
            return $this->sendResponse(new UserResource($user), 'User created successfully.');
        });
    }
}
