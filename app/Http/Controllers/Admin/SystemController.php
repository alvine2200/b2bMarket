<?php

namespace App\Http\Controllers\Admin;

use App\Models\System;
use App\Models\Support;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\SystemResource;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;

class SystemController extends BaseController
{
    public function change_system_details(Request $request)
    {


        $validator= Validator::make($request->all(), [
            'system_name'=>'required|string',
            'system_logo'=>'required|mimes:jpg,png,jpeg,gif,svg|max:10240',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $system_logo=$request->file('system_logo')->store('system_logo');

        $system=new System();
        $system->system_name = $request->system_name;
        $system->system_logo=$system_logo;

        $system->save();

        return $this->SendResponse(new SystemResource($system),'System edit is successful');
    }

    public function display_system()
    {
        $system=System::all();

        return $this->SendResponse(SystemResource::collection($system),'System name and logo is successfully displayed');

    }
}
