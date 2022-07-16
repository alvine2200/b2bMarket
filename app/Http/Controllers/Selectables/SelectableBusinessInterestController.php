<?php

namespace App\Http\Controllers\Selectables;


use App\Http\Controllers\BaseController;
use App\Models\Selectables\SelectableBusinessInterest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Selectables\SelectableBusinessInterest as SelectableBusinessInterestResource;

class SelectableBusinessInterestController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = SelectableBusinessInterest::query();
        if($q = $request->input('q')){
            $query->where('name', 'like', "%$q%")
                ->orWhere('type','like',"%$q%");
        }

        $businessInterests = $query->get();
        return $this->sendResponse(SelectableBusinessInterestResource::collection($businessInterests), 'Selectable Business Interests fetched.');
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
        $input = $request->all();
        // dd($input);
        $validator = Validator::make($input, [
            'name' => 'required',
            'type' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors(), code : 400);       
        }

        $businessInterest = SelectableBusinessInterest::create($input);
        return $this->sendResponse(new SelectableBusinessInterestResource($businessInterest), 'Selectable Business Interest created.');
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
        $businessInterest = SelectableBusinessInterest::find($id);
        if (is_null($businessInterest)) {
            return $this->sendError('Selectable Business Interest not found!');
        }
        return $this->sendResponse(new SelectableBusinessInterestResource($businessInterest), 'Selectable Business Interest retrieved.');
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
        $businessInterest = SelectableBusinessInterest::find($id);
        if (is_null($businessInterest)) {
            return $this->sendError('Selectable Business Interest not found!');
        }
        
        $input = $request->all();
        $businessInterest->update($input);

        return $this->sendResponse(new SelectableBusinessInterestResource($businessInterest), 'Selectable Business Interest successfully updated.');
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
        $businessInterest = SelectableBusinessInterest::find($id);
        if (is_null($businessInterest)) {
            return $this->sendError('Selectable Business Interest not found!');
        }

        $businessInterest->delete();
        return $this->sendResponse([], 'Selectable Business Interest deleted.');

    }
}
