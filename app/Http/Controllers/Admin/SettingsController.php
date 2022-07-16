<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\BaseController;
use App\Http\Resources\SettingsResource;
use Illuminate\Support\Facades\Validator;

class SettingsController extends BaseController
{
    public function settings(Request $request)
    {
        $profile_image =$request->file('profile_image')->store('profile_image');

        $validator= Validator::make($request->all(), [
            'full_name'=>'string|required',
            'admin_email'=>'string|required',
            'profile_image'=>'mimes:jpg,png,jpeg,gif,svg|max:10240',
            'password_confirmation'=>'min:6|max:20|required',
            'password'=>'required|confirmed',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $settings=User::where('is_super_admin',true)->first();
        $settings->full_name= $request->full_name;
        $settings->admin_email= $request->admin_email;
        $settings->password=Hash::make($request->password);
        $settings->profile_image=$profile_image;
        $settings->is_super_admin='1';
        $settings->save();

        return $this->SendResponse(new SettingsResource($settings),'Setting updated successfully');

    }

    public function show_settings()
    {
        $settings=User::where('is_super_admin',true)->get();

        return $this->SendResponse(SettingsResource::collection($settings),'Settings Retrieved successfully');
    }


}
