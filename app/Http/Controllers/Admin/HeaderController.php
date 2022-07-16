<?php

namespace App\Http\Controllers\Admin;

use App\Models\Header;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\HeaderResource;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;

class HeaderController extends BaseController
{
    public function add_headers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'header_nav'=>'required|string',
            'header_link'=>'required|string',
            'header_logo'=>'mimes:jpg,png,jpeg,gif,svg|max:10240',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $headers_logo=$request->file('headers_logo')->store('headers_logo');

        $headers=new Header();
        $headers->header_logo=$headers_logo;
        $headers->header_nav=$request->header_nav;
        $headers->header_link=$request->header_link;

        $headers->save();

        return $this->SendResponse(new HeaderResource($headers),'New header is added successfully');

    }
    public function edit_headers($id)
    {
        $headers=Header::find($id);
        if($headers == null)
        {
            return $this->SendError([],'Id not found');
        }

        return $this->SendResponse(new HeaderResource($headers),'all headers are fetched');
    }

    public function show_headers()
    {
        $headers=Header::all();

        return $this->SendResponse(HeaderResource::collection($headers),'all headers are fetched');
    }

    public function update_headers(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'header_nav'=>'required|string',
            'header_link'=>'required|string',
            'header_logo'=>'mimes:jpg,png,jpeg,gif,svg|max:10240',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $headers_logo=$request->file('headers_logo')->store('headers_logo');

        $headers=Header::find($id);
        if($headers == null)
        {
            return $this->SendError([],'Id not found');
        }
        $headers->header_logo=$headers_logo;
        $headers->header_nav=$request->header_nav;
        $headers->header_link=$request->header_link;

        $headers->update();

        return $this->SendResponse(new HeaderResource($headers),'Update is a success');

    }

    public function delete_headers($id)
    {
        $headers=Header::find($id);

        if($headers == null)
        {
            return $this->SendError([],'Id not found');
        }

        $headers->delete();

        return $this->SendError([],'Header deleted successfully');

    }


}
