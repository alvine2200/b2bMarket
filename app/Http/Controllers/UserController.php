<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;

class UserController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = User::query();
        
        if($q = $request->input('q')){
            $query->where('first_name', 'like', "%$q%")
                ->orWhere('middle_name','like',"%$q%")
                ->orWhere('last_name','like',"%$q%")
                ->orWhere('email','like',"%$q%")
                ->orWhere('secondary_email','like',"%$q%")
                ->orWhere('phone','like',"%$q%")
                ->orWhere('secondary_phone','like',"%$q%")
                ->orWhere('gender_name','like',"%$q%");
        }

        
        $countries = $query->get();

        return $this->sendResponse(UserResource::collection($countries), 'Users fetched.');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
