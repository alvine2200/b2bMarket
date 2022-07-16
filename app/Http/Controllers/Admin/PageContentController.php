<?php

namespace App\Http\Controllers\Admin;

use App\Models\PageContent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\BaseController;
use App\Http\Resources\PageContentResource;
use Illuminate\Support\Facades\Validator;

class PageContentController extends BaseController
{
    public function add(Request $request)
    {
        $validator= Validator::make($request->all(), [
            'title'=>'required|string',
            'link'=>'required|string',
            'content'=>'required|string',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $pages=PageContent::firstOrCreate([
            'title'=>$request->title,
            'link'=>$request->link,
            'content'=>$request->content,
            'status'=>'published',
        ]);

        return $this->SendResponse(new PageContentResource($pages),'New Content published successfully');

    }

    public function save(Request $request)
    {
        $validator= Validator::make($request->all(), [
            'title'=>'required|string',
            'link'=>'required|string',
            'content'=>'required|string',

        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $pages=PageContent::firstOrCreate([
            'title'=>$request->title,
            'link'=>$request->link,
            'content'=>$request->content,
            'status'=>'draft',
        ]);

        return $this->SendResponse(new PageContentResource($pages),'New Content drafted successfully');

    }

    public function show()
    {
        $pages=PageContent::where('status','published')->get();

        return $this->SendResponse(PageContentResource::collection($pages),'published content fetched');
    }

    public function show_draft()
    {
        $pages=PageContent::where('status','draft')->get();

        return $this->SendResponse(PageContentResource::collection($pages),'drafted content fetched');
     
    }

    public function delete($id)
    {
        $pages=PageContent::find($id);

        if($pages == null)
        {
            return $this->SendError([],'Sorry Id not found');
        }

        $pages->delete();
        return $this->SendResponse([],'content deleted successfully');
    }
    public function edit_draft($id)
    {
        $pages=PageContent::find($id);

        if($pages == null)
        {
            return $this->SendError([],'Sorry Id not found');
        }

        return $this->SendResponse(new PageContentResource($pages),'Content fetched successfully');
    }

    public function edit($id)
    {
        $pages=PageContent::find($id);

        if($pages == null)
        {
            return $this->SendError([],'Sorry Id not found');
        }

        return $this->SendResponse(new PageContentResource($pages),'Content fetched successfully');
    }

    public function update(Request $request,$id)
    {
        $validator= Validator::make($request->all(), [
            'title'=>'required|string',
            'link'=>'required|string',
            'content'=>'required|string',
        ]);

        if($validator->fails())
        {
            return $this->SendError('Error validation', $validator->errors(), 400);
        }

        $pages=PageContent::find($id);
        $pages->title=$request->title;
        $pages->link=$request->link;
        $pages->content=$request->content;

        $pages->update();

        return $this->SendResponse(new PageContentResource($pages),'updates is a success');
    }



}
