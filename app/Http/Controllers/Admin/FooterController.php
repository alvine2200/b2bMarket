<?php

namespace App\Http\Controllers\Admin;

use App\Models\Footer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\FooterResource;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;

class FooterController extends BaseController
{
    public function add_footers(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'footer_nav'=>'required|string',
        'footer_link'=>'required|string',

       ]);

         if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

    $footers=new Footer();
    $footers->footer_nav=$request->footer_nav;
    $footers->footer_link=$request->footer_link;

    $footers->save();

    return $this->SendResponse(new FooterResource($footers),'New Footer is added successfully');

}
public function show_footers()
{
    $footers=Footer::all();

    return $this->SendResponse(FooterResource::collection($footers),'footers fetched successfully');

}

public function edit_footers($id)
{
    $footers=Footer::find($id);

    if($footers == null)
    {
        return $this->SendError([],'Id not found');
    }

    return $this->SendResponse(new FooterResource($footers),'all footers are fetched');
}

public function update_footers(Request $request,$id)
{
    $validator = Validator::make($request->all(), [
        'footer_nav'=>'required|string',
        'footer_link'=>'required|string',
    ]);

    if($validator->fails())
    {
        return $this->SendError('Error validation', $validator->errors(), 400);
    }


    $footers=Footer::find($id);

    if($footers == null)
    {
        return $this->SendError([],'Id not found');
    }
    $footers->footer_nav=$request->footer_nav;
    $footers->footer_link=$request->footer_link;

    $footers->update();

    return $this->SendResponse(new FooterResource($footers),'Update is a success');

}

public function delete_footers($id)
{
    $footers=Footer::find($id);

    if($footers == null)
    {
        return $this->SendError([],'Id not found');
    }

    $footers->delete();

    return $this->SendError([],'Footer deleted successfully');

}
}
