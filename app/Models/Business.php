<?php

namespace App\Models;

use App\Models\ProductsServices\Product;
use App\Models\Profile\Client;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Selectables\SelectableCountry;
use App\Models\Selectables\SelectableBusinessSector;
use App\Models\Selectables\SelectableBusinessInterest;
use App\Models\Selectables\SelectableBusinessKeyword;
use App\Models\SocialNetworking\BusinessReview;
use App\Models\Selectables\SelectableBusinessService;
use App\Models\Selectables\SelectableBusinessProduct;
use App\Models\Selectables\SelectableBusinessPlatformNeed;
use App\Models\Selectables\SelectableBusinessPartnershipInterest;
use App\Models\Selectables\SelectableBusinessCommercialInterest;
use App\Models\Selectables\SelectableBusinessDistributionInterest;
use App\Models\Selectables\SelectableImpExpInterest;
use App\Models\Selectables\SelectableBusinessConsultingInterest;
use App\Models\Selectables\SelectableBusinessInvestingInterest;
use App\Models\Selectables\SelectableTechnology;
use App\Models\Selectables\SelectableValueChain;
use App\Models\Selectables\SelectableMakeInvestmentInterest;
use App\Models\Selectables\SelectableInvestorType;

class Business extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'name',
        'website',
        'email',
        'logo',
        'business_type',
        'phone',
        'headquarters_id',
        'main_sector_id',
        'investor_type_id',
        'incorporation_number',
        'clients',
        'registration_step',
        'platform_needs',
        'venture_sector_id',
        'expand_services',
        'executive_summary',
        'size_start_range',
        'size_end_range',
        'age_start_range',
        'age_end_range',
    ];
    // protected $fillable = [
    //     'headquarters_id',
    //     'main_sector_id',
    //     'incorporation_number',
    //     'name',
    //     'type',
    //     'country_id',
    //     'executive_summary',
    // ];
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function headquarters()
    {
        return $this->belongsTo(SelectableCountry::class, "headquarters_id");
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function services()
    {
        return $this->hasMany(Services::class);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    // public function getRouteKeyName()
    // {
    //     return [
    //         'slug',
    //         'headquarters_id',
    //         'main_sector_id'
    //     ];

    //     /*
    //     return 'slug';
    //     'headquarters_id',
    //     'main_sector_id',
    //     */
    // }

    public function countries(){
        return $this->belongsToMany(SelectableCountry::class, "business_country", "business_id", "country_id");
    }

    public function operating_countries(){
        return $this->belongsToMany(SelectableCountry::class, "business_operating_country", "business_id", "operating_country_id");
    }
    
    public function expand_countries(){
        return $this->belongsToMany(SelectableCountry::class, "business_expand_country", "business_id", "expand_country_id");
    }

    public function mainSector(){
        return $this->belongsTo(SelectableBusinessSector::class, "main_sector_id");
    }

    public function otherSectors(){
        return $this->belongsToMany(SelectableBusinessSector::class, "business_sector", "business_id", "sector_id");
    }

    public function interests(){
        return $this->belongsToMany(SelectableBusinessInterest::class, "business_interest", "business_id", "interest_id");
    }

    public function keywords(){
        return $this->belongsToMany(SelectableBusinessKeyword::class, "business_keyword", "business_id", "keyword_id");
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'business_id','id');
    }
    public function reviews(){
        return $this->hasMany(BusinessReview::class);
    }
    
    public function mainServices(){
        return $this->belongsToMany(SelectableBusinessService::class, "business_main_service", "business_id", "main_service_id");
    }
    
    public function mainProducts(){
        return $this->belongsToMany(SelectableBusinessProduct::class, "business_main_product", "business_id", "main_product_id");
    }
    
    public function platformNeeds(){
        return $this->belongsToMany(SelectableBusinessPlatformNeed::class, "business_platform_need", "business_id", "platform_need_id");
    }
    
    public function partnershipInterests(){
        return $this->belongsToMany(SelectableBusinessPartnershipInterest::class, "business_partnership_interest", "business_id", "partnership_interest_id");
    }
    
    public function commercialInterests(){
        return $this->belongsToMany(SelectableBusinessCommercialInterest::class, "business_commercial_interest", "business_id", "commercial_interest_id");
    }
    
    public function distributionInterests(){
        return $this->belongsToMany(SelectableBusinessDistributionInterest::class, "business_distribution_interest", "business_id", "distribution_interest_id");
    }
    
    public function impExpInterests(){
        return $this->belongsToMany(SelectableImpExpInterest::class, "business_imp_exp_interest", "business_id", "imp_exp_interest_id");
    }
    
    public function consultingInterests(){
        return $this->belongsToMany(SelectableBusinessConsultingInterest::class, "business_consulting_interest", "business_id", "consulting_interest_id");
    }

    public function investingInterests(){
        return $this->belongsToMany(SelectableBusinessInvestingInterest::class, "business_investing_interest", "business_id", "investing_interest_id");
    }
    
    public function serviceInterests(){
        return $this->belongsToMany(SelectableBusinessService::class, "business_service_interest", "business_id", "service_interest_id");
    }
    
    public function technologyInterests(){
        return $this->belongsToMany(SelectableTechnology::class, "business_technology_interest", "business_id", "technology_interest_id");
    }
    
    public function valueChainsDealingWith(){
        return $this->belongsToMany(SelectableValueChain::class, "business_value_chain", "business_id", "value_chain_id");
    }
    
    public function otherServices(){
        return $this->belongsToMany(SelectableBusinessService::class, "business_other_service", "business_id", "service_id");
    }

    public function sectorInterests(){
        return $this->belongsToMany(SelectableBusinessSector::class, "business_sector_interest", "business_id", "sector_id");
    }
    
    public function makeInvestmentInterests(){
        return $this->belongsToMany(SelectableMakeInvestmentInterest::class, "business_make_investment_interest", "business_id", "investment_interest_id");
    }
    
    public function countryInterests(){
        return $this->belongsToMany(SelectableCountry::class, "business_country_interest", "business_id", "country_id");
    }
    
    public function productInterests(){
        return $this->belongsToMany(SelectableBusinessProduct::class, "business_product_interest", "business_id", "product_id");
    }

    public function userLike(){
        return $this->belongsToMany(User::class, "user_business_like_dislike");
    }

    public function investorType(){
        return $this->belongsTo(SelectableInvestorType::class, "investor_type_id");
    }

    // protected function selectable_country()
    // {
    //     return $this->hasMany(SelectableCountry::class);
    // }
    public function teamMembers(){
        return $this->hasMany(User::class, "teambusiness_id");
    }

    public function clients(){
        return $this->hasMany(Client::class);
    }

    public function usersViewedProfile(){
        return $this->belongsToMany(User::class, "profile_views")->withTimestamps();
    }
}
