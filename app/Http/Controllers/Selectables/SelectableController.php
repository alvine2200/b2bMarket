<?php

namespace App\Http\Controllers\Selectables;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use \App\Models\Selectables\SelectableBusinessService;
use App\Models\Selectables\SelectableBusinessProduct;
use App\Models\Selectables\SelectableBusinessPlatformNeed;
use App\Models\Selectables\SelectableBusinessPartnershipInterest;

use App\Http\Resources\Selectables\Selectable as SelectableResource;
use App\Models\Selectables\SelectableBusinessCommercialInterest;
use App\Models\Selectables\SelectableBusinessConsultingInterest;
use App\Models\Selectables\SelectableBusinessDistributionInterest;
use App\Models\Selectables\SelectableBusinessInvestingInterest;
use App\Models\Selectables\SelectableBusinessSector;
use App\Models\Selectables\SelectableBusinessRole;
use App\Models\Selectables\SelectableCountry;
use App\Models\Selectables\SelectableImpExpInterest;
use App\Models\Selectables\SelectableTechnology;
use App\Models\Selectables\SelectableValueChain;
use App\Models\Selectables\SelectableInvestorType;
use App\Models\Selectables\SelectableContinent;

class SelectableController extends BaseController
{
    //
    public function businessSectors(Request $request){
        // SelectableServices::
        $query = SelectableBusinessSector::query();
        if($q = $request->input('q')){
            $query->where('name', 'like', "%$q%");
        }

        $businessSectors = $query->get();
        return $this->sendResponse(SelectableResource::collection($businessSectors), 'Selectable Business Sectors fetched.');
    }
    
    public function roles(Request $request){
        // SelectableServices::
        $query = SelectableBusinessRole::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $roles = $query->get();

        return $this->sendResponse(SelectableResource::collection($roles), 'Selectable Business Roles fetched.');
    }
    
    public function countries(Request $request){
        // SelectableServices::
        $query = SelectableCountry::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $countries = $query->get();

        return $this->sendResponse(SelectableResource::collection($countries), 'Selectable Countries fetched.');
    }
    
    public function africanCountries(Request $request){
        // SelectableServices::
        $query = SelectableCountry::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $countries = $query->where('continent', 'Africa')->get();

        return $this->sendResponse(SelectableResource::collection($countries), 'Selectable Countries fetched.');
    }
    
    public function nonAfricanCountries(Request $request){
        // SelectableServices::
        $query = SelectableCountry::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $countries = $query->where('continent', '!=', 'Africa')->get();

        return $this->sendResponse(SelectableResource::collection($countries), 'Selectable Countries fetched.');
    }
    
    public function services(Request $request){
        // SelectableServices::
        $query = SelectableBusinessService::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $countries = $query->get()->sortBy('name');

        return $this->sendResponse(SelectableResource::collection($countries), 'Selectable Services fetched.');
    }
    
    public function products(Request $request){
        // SelectableServices::
        $query = SelectableBusinessProduct::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $products = $query->get()->sortBy('name');

        return $this->sendResponse(SelectableResource::collection($products), 'Selectable Products fetched.');
    }
    
    public function platformNeeds(Request $request){
        // SelectableServices::
        $query = SelectableBusinessPlatformNeed::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $platformNeeds = $query->get();

        return $this->sendResponse(SelectableResource::collection($platformNeeds), 'Selectable Platform Needs fetched.');
    }
    
    public function partnershipInterests(Request $request){
        // SelectableServices::
        $query = SelectableBusinessPartnershipInterest::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $partnershipInterests = $query->get();

        return $this->sendResponse(SelectableResource::collection($partnershipInterests), 'Selectable Partnership Interests fetched.');
    }
    
    public function commercialInterests(Request $request){
        // SelectableServices::
        $query = SelectableBusinessCommercialInterest::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $commercialInterests = $query->get();

        return $this->sendResponse(SelectableResource::collection($commercialInterests), 'Selectable Commercial Interests fetched.');
    }
    
    public function distributionInterests(Request $request){
        // SelectableServices::
        $query = SelectableBusinessDistributionInterest::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $distributionInterests = $query->get();

        return $this->sendResponse(SelectableResource::collection($distributionInterests), 'Selectable Distribution Interests fetched.');
    }
    
    public function impExpInterests(Request $request){
        // SelectableServices::
        $query = SelectableImpExpInterest::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $impExpInterests = $query->get();

        return $this->sendResponse(SelectableResource::collection($impExpInterests), 'Selectable Import Export Interests fetched.');
    }
    
    public function consultingInterests(Request $request){
        // SelectableServices::
        $query = SelectableBusinessConsultingInterest::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $consultingInterests = $query->get();

        return $this->sendResponse(SelectableResource::collection($consultingInterests), 'Selectable Consulting Interests fetched.');
    }
    
    public function investingInterests(Request $request){
        // SelectableServices::
        $query = SelectableBusinessInvestingInterest::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $investingInterests = $query->get();

        return $this->sendResponse(SelectableResource::collection($investingInterests), 'Selectable Investing Interests fetched.');
    }
    
    public function technologyInterests(Request $request){
        // SelectableServices::
        $query = SelectableTechnology::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $technologyInterests = $query->get();

        return $this->sendResponse(SelectableResource::collection($technologyInterests), 'Selectable Technology Interests fetched.');
    }
    
    public function valueChains(Request $request){
        // SelectableServices::
        $query = SelectableValueChain::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $valueChains = $query->get();

        return $this->sendResponse(SelectableResource::collection($valueChains), 'Selectable Value Chains fetched.');
    }
    
    public function investorTypes(Request $request){
        // SelectableServices::
        $query = SelectableInvestorType::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $valueChains = $query->get();

        return $this->sendResponse(SelectableResource::collection($valueChains), 'Selectable Investor Types fetched.');
    }
    
    public function continents(Request $request){
        // SelectableServices::
        $query = SelectableContinent::query();

        if ($q = $request->input('q')) {
            $query->where('name', 'like', "%$q%");
        }

        $continents = $query->get();

        return $this->sendResponse(SelectableResource::collection($continents), 'Selectable Continents fetched.');
    }
}
