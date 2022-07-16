<?php

namespace App\Http\Controllers\Selectables;

use App\Http\Controllers\BaseController;
use App\Models\Selectables\SelectableBusinessKeyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\Selectables\SelectableBusinessKeyword as SelectableBusinessKeywordResource;

class SelectableBusinessKeywordController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $query = SelectableBusinessKeyword::query();
        if($q = $request->input('q')){
            $query->where('name', 'like', "%$q%")
                ->orWhere('type','like',"%$q%");
        }

        $businessKeywords = $query->get();
        return $this->sendResponse(SelectableBusinessKeywordResource::collection($businessKeywords), 'Selectable Business Keywords fetched.');
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

        $businessKeyword = SelectableBusinessKeyword::create($input);
        return $this->sendResponse(new SelectableBusinessKeywordResource($businessKeyword), 'Selectable Business Keyword created.');
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
        $businessKeyword = SelectableBusinessKeyword::find($id);
        if (is_null($businessKeyword)) {
            return $this->sendError('Selectable Business Keyword not found!');
        }
        return $this->sendResponse(new SelectableBusinessKeywordResource($businessKeyword), 'Selectable Business Keyword retrieved.');
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
        $businessKeyword = SelectableBusinessKeyword::find($id);
        if (is_null($businessKeyword)) {
            return $this->sendError('Selectable Business Keyword not found!');
        }
        
        $input = $request->all();
        $businessKeyword->update($input);

        return $this->sendResponse(new SelectableBusinessKeywordResource($businessKeyword), 'Selectable Business Keyword successfully updated.');
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
        $businessKeyword = SelectableBusinessKeyword::find($id);
        if (is_null($businessKeyword)) {
            return $this->sendError('Selectable Business Keyword not found!');
        }

        $businessKeyword->delete();
        return $this->sendResponse([], 'Selectable Business Keyword deleted.');

    }
}
