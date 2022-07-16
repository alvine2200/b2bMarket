<?php

namespace App\Http\Controllers\Admin;

use App\Models\UserNews;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;
use App\Http\Resources\UserNewsResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\NewsInformationResource;

class NewsInformationController extends BaseController
{
    public function post_news(Request $request)
    {


        $validator=Validator::make($request->all(),[
            'author'=>'required|string',
            'title'=>'required|string',
            'subtitle'=>'string',
            'body'=>'required',
            'image'=>'mimes:jpg,png,jpeg,gif,svg|max:10240',
        ]);

        if($validator->fails()) {
            return $this->SendError('validation failed',$validator->errors(),400);
        }

        $image=$request->file('image')->store('image');

        $news=new UserNews();
        $news->user_id =$request->user_id;//Auth::user()->id;
        $news->title=$request->title;
        $news->subtitle=$request->subtitle;
        $news->body=$request->body;
        $news->image=$image;
        $news->status='approved';
        $news->author='System Admin';

        $news->save();

        return $this->SendResponse(new NewsInformationResource($news),'Post successfully added');
    }
    public function get_all_news()
    {
        $news= UserNews::all();

        return $this->sendResponse(NewsInformationResource::collection($news), 'all news are listed');
    }

    public function show_user_news($id)
    {
        $news= UserNews::find($id);

        if(!$news){
            return $this->SendError('Id not found', []);
        }

        return $this->sendResponse(new NewsInformationResource($news),'all User news are listed successfully');
    }

    public function delete_news($id)
    {
        $news=UserNews::find($id);

        if($news == null)
        {
            return $this->SendError('Sorry, Id Not Found', []);
        }

        $news->delete();
        return $this->sendResponse([],'News Successfully Deleted');
    }

    public function approve_news($id)
    {
        $news=UserNews::find($id);

        if($news == null)
        {
            return $this->SendError('Sorry, Id Not Found', []);
        }

        $news->status = 'approved';

        $news->update();

        return $this->sendResponse(new NewsInformationResource($news),'News Approved successfully');

    }

    public function display_news()
    {
        $news= UserNews::where('status','=','approved')->get();

        return $this->sendResponse(NewsInformationResource::collection($news),'All Approved news Posted');
    }

}
