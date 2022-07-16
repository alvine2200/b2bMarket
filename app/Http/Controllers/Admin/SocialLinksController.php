<?php

namespace App\Http\Controllers\Admin;

use App\Models\SocialLink;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\SocialLinkResource;

class SocialLinksController extends BaseController
{
    public function add_social_links(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'name'=>'required|string',
        'url'=>'required|string',
        'icon'=>'mimes:jpg,png,jpeg,gif,svg|max:10240',

       ]);

         if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $icon=$request->file('icon')->store('icon');

       $social_links=SocialLink::firstOrCreate([
        'name'=>$request->name,
        'url'=>$request->url,
        'icon'=>$icon,
       ]);

       return $this->SendResponse(new SocialLinkResource($social_links),'New link is added successfully');

    }

    public function show_social_links()
    {
        $social_links=SocialLink::all();

        return $this->SendResponse(SocialLinkResource::collection($social_links),'Social links fetched successfully');

    }

    public function edit_social_links($id)
    {
        $social_links=SocialLink::find($id);

        if($social_links == null)
        {
            return $this->SendError([],'Id not found');
        }

        return $this->SendResponse(new SocialLinkResource($social_links),'links is fetched');
    }

    public function update_social_links(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'name'=>'required|string',
            'url'=>'required|string',
            'icon'=>'mimes:jpg,png,jpeg,gif,svg|max:10240',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $icon=$request->file('icon')->store('icon');

        $social_links=SocialLink::find($id);

        if($social_links == null)
        {
            return $this->SendError([],'sorry, Id not found');
        }
        $social_links->name=$request->name;
        $social_links->url=$request->url;
        $social_links->icon=$icon;

        $social_links->update();

        return $this->SendResponse(new SocialLinkResource($social_links),'Update is a success');

    }

    public function delete_social_links($id)
    {
        $social_links=SocialLink::find($id);

        if($social_links == null)
        {
            return $this->SendError([],'Id not found');
        }

        $social_links->delete();

        return $this->SendResponse([],'Footer deleted successfully');

    }

}
