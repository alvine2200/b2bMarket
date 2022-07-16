<?php

namespace App\Http\Controllers\Profiling;


use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\Http\Resources\Profiling\Business as BusinessResource;

class ProfilingController extends BaseController
{
    private function getBusiness(){
        $authUser = User::find(Auth::user()->id);
        if($authUser->business()->doesntExist()){
            $business = $authUser->business()->create([]);
        }
        else{
            $business = $authUser->business;
        }

        return $business;
    }

    //
    public function setHeadquarters(Request $request){
        $validator = Validator::make($request->all(), [
            "headquarters_id" => "required"
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = $this->getBusiness();

        $business->headquarters_id = $request->all()["headquarters_id"];
        $business->save();

        return $this->sendResponse(new BusinessResource($business), "Headquarters saved successfully");
    }

    public function setCountriesWhereActive(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "countries" => "required|array"
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = $this->getBusiness();

        $business->operating_countries()->sync($input["countries"]);
        $business->save();

        return $this->sendResponse(new BusinessResource($business), "Countries where active saved successfully");
    }

    public function setMainSector(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "main_sector_id" => "required|int"
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = $this->getBusiness();

        $business->main_sector_id = $input["main_sector_id"];
        $business->save();

        return $this->sendResponse(new BusinessResource($business), "Main Sector saved successfully");
    }

    public function setOtherSectors(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "other_sectors" => "required|array"
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = $this->getBusiness();

        $business->otherSectors()->sync($input["other_sectors"]);
        $business->save();

        return $this->sendResponse(new BusinessResource($business), "Other sectors saved successfully");
    }

    public function setIncorporationNumber(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "incorporation_number" => "required|unique:businesses"
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = $this->getBusiness();

        $business->incorporation_number = $input["incorporation_number"];
        $business->save();

        return $this->sendResponse(new BusinessResource($business), "Incorporation number saved successfully");
    }

    public function uploadCompanyLogo(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "logo" => "required|mimes:jpg,jpeg,png"
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = $this->getBusiness();
        if($business->logo != null){
            Storage::delete($business->logo);
        }

        $path = $request->file('logo')->store('logos');
        $business->logo = $path;
        $business->save();

        return $this->sendResponse(new BusinessResource($business), "Logo uploaded successfully");
    }

    public function uploadCompanyBanner(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "banner" => "required|mimes:jpg,jpeg,png|max:10240"
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = $this->getBusiness();
        if($business->banner != null){
            Storage::delete($business->banner);
        }

        $path = $request->file('banner')->store('banners');
        $business->banner = $path;
        $business->save();

        return $this->sendResponse(new BusinessResource($business), "Banner uploaded successfully");
    }

    public function uploadCertificateOfIncorporation(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "certificate_of_incorporation" => "required|mimes:jpg,jpeg,png|max:10240"
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $authUser = User::find(Auth::user()->id);
        if($authUser->business()->doesntExist()){
            return $this->sendError('Business not found', ['details' => 'Business not found']);
        }

        $business = $authUser->business;
        if($business->certificate_of_incorporation != null){
            Storage::delete($business->certificate_of_incorporation);
        }

        $path = $request->file('certificate_of_incorporation')->store('certificates_of_incorporation');
        $business->certificate_of_incorporation = $path;
        $business->save();

        return $this->sendResponse(new BusinessResource($business), "Certificate of Incorporation uploaded successfully");
    }

    public function setExecutiveSummary(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "executive_summary" => "required|unique:businesses"
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = $this->getBusiness();

        $business->executive_summary = $input["executive_summary"];
        $business->save();

        return $this->sendResponse(new BusinessResource($business), "Executive summary saved successfully");
    }

    public function setBusinessInterests(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "interests" => "required|array"
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = $this->getBusiness();

        $business->interests()->sync($input["interests"]);
        $business->update(["profiling_percentage" => 0]);

        return $this->sendResponse(new BusinessResource($business), "Business interests saved successfully");
    }

    public function setBusinessKeywords(Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            "keywords" => "required|array"
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = $this->getBusiness();

        $business->keywords()->sync($input["keywords"]);
        $business->update(["profiling_percentage" => 0]);

        return $this->sendResponse(new BusinessResource($business), "Business keywords saved successfully");
    }
}
