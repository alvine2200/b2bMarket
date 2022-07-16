<?php

namespace App\Http\Controllers\Admin;

use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PagesResource;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Validator;

class PagesController extends BaseController
{
    public function add_pages(Request $request)
    {
        $validator= Validator::make($request->all(), [
            'name'=>'required|string',
            'url'=>'required|string',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $pages=Page::firstOrCreate([
            'name'=>$request->name,
            'url'=>$request->url,
            'status'=>'draft',
        ]);

        return $this->SendResponse(new PagesResource($pages),'New page created successfully');
    }

    public function show_pages()
    {
        $pages=Page::all();
        return $this->SendResponse(PagesResource::collection($pages),'pages fetched successfully');
    }

    public function edit_pages($id)
    {
        $pages=Page::find($id);

        if($pages == null)
        {
            return $this->SendError([],'Sorry, Id not found');
        }

        return $this->SendResponse(new PagesResource($pages),'Page Successfully fetched');
    }

    public function update_pages(Request $request,$id)
    {
        $validator= Validator::make($request->all(), [
            'name'=>'required|string',
            'url'=>'required|string',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $pages=Page::find($id);

        $pages->name= $request->name;
        $pages->url= $request->url;

        $pages->update();

        return $this->SendResponse(new PagesResource($pages),'New page updated successfully');

    }

    public function publish_pages($id)
    {
        $pages=Page::find($id);
        $pages->status='Published';

        return $this->SendResponse(new PagesResource($pages),'New page published successfully');
    }

    public function delete_pages($id)
    {
        $pages=Page::find($id);

        if($pages===null)
        {
            return $this->SendError([],'Sorry , Id not found');
        }

        $pages->delete();
        return $this->SendResponse([],'Successfully deleted');
    }

    public function unpublish_pages($id)
    {
        $pages=Page::find($id);

        if($pages===null)
        {
            return $this->SendError([],'Sorry , Id not found');
        }

        $pages->status='draft';

        return $this->SendResponse(new PagesResource($pages),'Page Unpublished successfully');
    }

}
