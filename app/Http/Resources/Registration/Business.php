<?php

namespace App\Http\Resources\Registration;

use App\Http\Resources\Selectables\Selectable;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class Business extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        if($this->business_type != "Investor")
        return [
            "business_type" => $this->business_type,
            "slug" => $this->slug,
            "name" => $this->name,
            "email" => $this->email,
            "phone" => $this->phone,
            "representative_full_name" => $this->user != null ? $this->user->full_name : '',
            "representative_role" => $this->user != null ? $this->user->businessRole?->name : '',
            "headquarters" => $this->headquarters != null? $this->headquarters->name: '',
            "operating_countries" => Selectable::collection($this->operating_countries),
            "expand_countries" => Selectable::collection($this->expand_countries),
            "main_sector" => $this->mainSector->name,
            "logo" => !empty($this->logo) ? url(Storage::url($this->logo)) : '',
            "banner" => !empty($this->banner) ? url(Storage::url($this->banner)) : '',
            "certificate_of_incorporation" => !empty($this->certificate_of_incorporation) ? url(Storage::url($this->certificate_of_incorporation)) : '',
            "incorporation_number" => $this->incorporation_number,
            "main_services" => Selectable::collection($this->mainServices),
            "main_products" => Selectable::collection($this->mainProducts),
            "platform_needs" => Selectable::collection($this->platformNeeds),
            "partnership_interests" => Selectable::collection($this->partnershipInterests),
            "commercial_interests" => Selectable::collection($this->commercialInterests),
            "distribution_interests" => Selectable::collection($this->distributionInterests),
            "imp_exp_interests" => Selectable::collection($this->impExpInterests),
            "consulting_interests" => Selectable::collection($this->consultingInterests),
            "investing_interests" => Selectable::collection($this->investingInterests),
            "country_interests" => Selectable::collection($this->countryInterests),
            "product_interests" => Selectable::collection($this->productInterests),
            "service_interests" => Selectable::collection($this->serviceInterests),
            "technology_interests" => Selectable::collection($this->technologyInterests),
            "value_chains_dealing_with" => Selectable::collection($this->valueChainsDealingWith),
            "executive_summary" => $this->executive_summary,
            "executive_summary_file" => !empty($this->executive_summary_file) ? url(Storage::url($this->executive_summary_file)) : '',
            "size" => "$this->size_start_range-$this->size_end_range",
            "age" => "$this->age_start_range-$this->age_end_range",
        ];
        
        return [
            "business_type" => $this->business_type,
            "slug" => $this->slug,
            "name" => $this->name,
            "email" => $this->email,
            "phone" => $this->phone,
            "representative_full_name" => $this->user != null ? $this->user->full_name : '',
            "representative_role" => $this->user != null ? $this->user->businessRole?->name : '',
            "headquarters" => $this->headquarters != null? $this->headquarters->name: '',
            "operating_countries" => Selectable::collection($this->operating_countries),
            "expand_countries" => Selectable::collection($this->expand_countries),
            "investor_type" => $this->investorType?->name || $this->user?->investorType?->name,
            "logo" => !empty($this->logo) ? url(Storage::url($this->logo)) : '',
            "banner" => !empty($this->banner) ? url(Storage::url($this->banner)) : '',
            "certificate_of_incorporation" => !empty($this->certificate_of_incorporation) ? url(Storage::url($this->certificate_of_incorporation)) : '',
            "incorporation_number" => $this->incorporation_number,
            "commercial_interests" => Selectable::collection($this->commercialInterests),
            "distribution_interests" => Selectable::collection($this->distributionInterests),
            "imp_exp_interests" => Selectable::collection($this->impExpInterests),
            "consulting_interests" => Selectable::collection($this->consultingInterests),
            "investing_interests" => Selectable::collection($this->investingInterests),
            "country_interests" => Selectable::collection($this->countryInterests),
            "product_interests" => Selectable::collection($this->productInterests),
            "service_interests" => Selectable::collection($this->serviceInterests),
            "technology_interests" => Selectable::collection($this->technologyInterests),
            "value_chains_dealing_with" => Selectable::collection($this->valueChainsDealingWith),
            "executive_summary" => $this->executive_summary,
            "executive_summary_file" => !empty($this->executive_summary_file) ? url(Storage::url($this->executive_summary_file)) : '',
            "size" => "$this->size_start_range-$this->size_end_range",
            "age" => "$this->age_start_range-$this->age_end_range",
        ];
    }
}
