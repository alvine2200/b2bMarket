<?php

namespace App\Http\Controllers\Admin;

use App\Models\About;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AboutResource;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;

class AboutController extends BaseController
{
    public function add(Request $request)
    {
        $validator= Validator::make($request->all(), [
            'about'=>'required|string',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $about=About::firstOrCreate([
            'about'=>$request->about,
        ]);

        return $this->SendResponse(new AboutResource($about),'posted successfully');
    }

    public function update(Request $request,$id)
    {
        $validator= Validator::make($request->all(), [
            'about'=>'required|string',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $about=About::find($id);

        $about->about=$request->about;

        $about->update();

        return $this->SendResponse(new AboutResource($about),'About updated successfully');
    }

    public function delete($id)
    {
        $about=About::find($id);
        $about->delete();
        return $this->SendResponse([],'About deleted successfully');
    }

    public function edit($id)
    {
        $about=About::find($id);
        
        return $this->SendResponse(new AboutResource($about),'About retrieved successfully');

    }

    public function show()
    {
        $about=About::all();
        return $this->SendResponse(AboutResource::collection($about),'Collection retrieved successfully');
    }
}
