<?php

namespace App\Http\Controllers;

use App\Models\UserNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BaseController;
use App\Http\Resources\UserNewsResource;
use Illuminate\Support\Facades\Validator;

class UserNewsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
           $news= UserNews::all();
           return $this->SendResponse(UserNewsResource::collection($news),'News Home');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $validator= Validator::make($request->all(), [
            'author'=>'required|string',
            'title'=>'required|string',
            'subtitle'=>'string',
            'body'=>'required',
            'image'=>'mimes:jpg,png,jpeg,gif,svg|max:10240',
        ]);

        if($validator->fails())
        {
            return $this->SendError('validation error',$validator->errors(), 400);
        }

        $image= $request->file('image')->store('image');

        $news= new UserNews();

        $news->user_id =$request->user_id;//Auth::user()->id;
        $news->title=$request->title;
        $news->subtitle=$request->subtitle;
        $news->body=$request->body;
        $news->author=$request->author;
        $news->image=$image;
        $news->status='pending';

        $news->save();

        return $this->SendResponse(new UserNewsResource($news),'News successfully published');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $news= UserNews::find($id);

        return $this->SendResponse(UserNewsResource::collection($news),'News Successfully Retrieved');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $image = $request->file('image')->store('image');

        $validator= Validator::make($request->all(), [
            'author'=>'required|string',
            'title'=>'required|string',
            'subtitle'=>'string',
            'body'=>'required',
            'image'=>'mimes:jpg,png,jpeg,gif,svg|max:10240',
        ]);

        if($validator->fails())
        {
            return $this->SendError('validation error',$validator->errors(), 400);
        }

        $news= UserNews::find($id);

        $news->user_id =Auth::user()->id;
        $news->title=$request->title;
        $news->subtitle=$request->subtitle;
        $news->body=$request->body;
        $news->author=$request->author;
        $news->image=$image;
        $news->status='pending';

        $news->update();

        return $this->sendResponse(new UserNewsResource($news),'News Successfully Updated');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $news= UserNews::find($id);
        if($news == null)
        {
            return $this->SendError([], 'Id not found');
        }

        $news->delete();

        return $this->sendResponse([],'News successfully deleted');
    }

}
