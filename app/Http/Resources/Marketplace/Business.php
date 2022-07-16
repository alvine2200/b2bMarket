<?php

namespace App\Http\Resources\Marketplace;

use App\Http\Resources\Selectables\Selectable;
use App\Http\Resources\Selectables\SelectableName;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class Business extends JsonResource
{
    private function getKeywords(){
        $keywords = collect();
        // if($this->operating_countries != null) $keywords = $keywords->merge($this->operating_countries);
        // if($this->expand_countries != null) $keywords = $keywords->merge($this->expand_countries);
        if($this->main_products != null) $keywords = $keywords->merge($this->main_products);
        if($this->main_services != null) $keywords = $keywords->merge($this->main_services);
        if($this->technologyInterests != null) $keywords = $keywords->merge($this->technologyInterests);
        if($this->valueChainsDealingWith != null) $keywords = $keywords->merge($this->valueChainsDealingWith);
        return SelectableName::collection($keywords);
    }
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user->id,
            "business_type" => $this->business_type,
            "slug" => $this->slug,
            "name" => $this->name,
            "logo" => !empty($this->logo) ? url(Storage::url($this->logo)) : '',
            "banner" => !empty($this->banner) ? url(Storage::url($this->banner)) : '',
            "executive_summary" => $this->executive_summary,
            "executive_summary_file" => !empty($this->executive_summary_file) ? url(Storage::url($this->executive_summary_file)) : '',
            "email" => $this->email,
            "phone" => $this->phone,
            "headquarters" => $this->headquarters?->name,
            "operating_countries" => Selectable::collection($this->operating_countries),
            "expand_countries" => Selectable::collection($this->expand_countries),
            "main_services" => Selectable::collection($this->mainServices),
            "main_products" => Selectable::collection($this->mainProducts),
            "is_liked" => User::find(Auth::user()->id)->getIsBusinessLikedAttribute($this->id),
            "is_disliked" => User::find(Auth::user()->id)->getIsBusinessDisikedAttribute($this->id),
            "is_saved" => User::find(Auth::user()->id)->getIsBusinessSavedAttribute($this->id),
            "is_followed" => User::find(Auth::user()->id)->getIsBusinessFollowedAttribute($this->id),
            "size" => $this->size_start_range."-".$this->size_end_range,
            "age" => $this->age_start_range."-".$this->age_end_range,
            "keywords" => $this->getKeywords(),
            // todo: add random product
        ];
        // return [
        //     "business_type" => $this->business_type,
        //     "slug" => $this->slug,
        //     "name" => $this->name,
        //     "email" => $this->email,
        //     "phone" => $this->phone,
        //     "representative_full_name" => $this->user->full_name,
        //     "representative_role" => $this->user->businessRole->name,
        //     "headquarters" => $this->headquarters->name,
        //     "operating_countries" => Selectable::collection($this->operating_countries),
        //     "expand_countries" => Selectable::collection($this->expand_countries),
        //     "main_sector" => $this->mainSector->name,
        //     "logo" => $this->logo,
        //     "banner" => $this->banner,
        //     "certificate_of_incorporation" => $this->certificate_of_incorporation,
        //     "incorporation_number" => $this->incorporation_number,
        //     "main_services" => Selectable::collection($this->mainServices),
        //     "main_products" => Selectable::collection($this->mainProducts),
        //     "platform_needs" => Selectable::collection($this->platformNeeds),
        //     "partnership_interests" => Selectable::collection($this->partnershipInterests),
        //     "commercial_interests" => Selectable::collection($this->commercialInterests),
        //     "distribution_interests" => Selectable::collection($this->distributionInterests),
        //     "imp_exp_interests" => Selectable::collection($this->impExpInterests),
        //     "consulting_interests" => Selectable::collection($this->consultingInterests),
        //     "investing_interests" => Selectable::collection($this->investingInterests),
        //     "service_interests" => Selectable::collection($this->serviceInterests),
        //     "technology_interests" => Selectable::collection($this->technologyInterests),
        //     "value_chains_dealing_with" => Selectable::collection($this->valueChainsDealingWith),
        //     "executive_summary" => $this->executive_summary,
        //     "executive_summary_file" => $this->executive_summary_file,
        // ];
    }
}
