<?php

namespace App\Http\Resources\Profile;

use App\Http\Resources\Selectables\SelectableName;
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
        return [
            "id" => $this->id,
            "slug" => $this->slug,
            "business_type" => $this->business_type,
            "name" => $this->name,
            "email" => $this->email,
            "executive_summary" => $this->executive_summary,
            "logo" => !empty($this->logo) ? url(Storage::url($this->logo)) : '',
            "banner" => !empty($this->banner) ? url(Storage::url($this->banner)) : '',
            "executive_summary_file" => !empty($this->executive_summary_file) ? url(Storage::url($this->executive_summary_file)) : '',
            "headquarters" => $this->headquarters->name,
            "operating_countries" => SelectableName::collection($this->operating_countries),
            "expand_countries" => SelectableName::collection($this->expand_countries),
            "main_sector" => $this->mainSector?->name ?? '',
            "main_services" => SelectableName::collection($this->mainServices),
            "main_products" => SelectableName::collection($this->mainProducts),
            "service_interests" => SelectableName::collection($this->serviceInterests),
            "technology_interests" => SelectableName::collection($this->technologyInterests),
            "value_chains_dealing_with" => SelectableName::collection($this->valueChainsDealingWith),
            "website" => $this->website ?? '',
            "linkedin_link" => $this->linkedin_link ?? '',
            "facebook_link" => $this->facebook_link ?? '',
            "twitter_link" => $this->twitter_link ?? '',

        ];
    }
}
