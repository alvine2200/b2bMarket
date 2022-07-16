<?php

namespace App\Http\Controllers\Selectables;

use App\Http\Controllers\BaseController;
use App\Models\Selectables\SelectableBusinessSector;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Selectables\SelectableBusinessSector as SelectableBusinessSectorResource;

class SelectableBusinessSectorController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = SelectableBusinessSector::query();
        if($q = $request->input('q')){
            $query->where('name', 'like', "%$q%");
        }

        $businessSectors = $query->get();
        return $this->sendResponse(SelectableBusinessSectorResource::collection($businessSectors), 'Selectable Business Sectors fetched.');
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
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors(), code : 400);       
        }

        $businessSector = SelectableBusinessSector::create($input);
        return $this->sendResponse(new SelectableBusinessSectorResource($businessSector), 'Selectable Business Sector created.');
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
        $businessSector = SelectableBusinessSector::find($id);
        if (is_null($businessSector)) {
            return $this->sendError('Selectable Business Sector not found!');
        }
        return $this->sendResponse(new SelectableBusinessSectorResource($businessSector), 'Selectable Business Sector retrieved.');
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
        $businessSector = SelectableBusinessSector::find($id);
        if (is_null($businessSector)) {
            return $this->sendError('Selectable Business Sector not found!');
        }
        
        $input = $request->all();
        $businessSector->update($input);

        return $this->sendResponse(new SelectableBusinessSectorResource($businessSector), 'Selectable Business Sector successfully updated.');
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
        $businessSector = SelectableBusinessSector::find($id);
        if (is_null($businessSector)) {
            return $this->sendError('Selectable Business Sector not found!');
        }

        $businessSector->delete();
        return $this->sendResponse([], 'Selectable Business Sector deleted.');

    }
}
