<?php

namespace App\Models;

use App\Models\ProductsServices\Cart;
use App\Models\ProductsServices\Product;
use App\Models\ProductsServices\DeliveryAddress;
use App\Models\Profile\CallToAction;
use App\Models\SocialNetworking\ChatRoom;
use App\Models\SocialNetworking\BusinessReview;
use App\Models\Wallet\Wallet;
use App\Models\Selectables\SelectableBusinessRole;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

use App\Models\Selectables\SelectableCountry;
use App\Models\Selectables\SelectableInvestorType;
use App\Models\SocialNetworking\Conversation;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'full_name',
        'password',
        'business_role_id',
        'investor_type_id',
        'is_business_rep',
        'is_investor',
        'is_employee',
        'is_super_admin',
        'profile_image',
        'quote',
        'is_team_member',
        'teambusiness_id',
    ];
    // protected $fillable = [
    //     'first_name',
    //     'middle_name',
    //     'last_name',
    //     'business_role_id',
    //     'gender_id',
    //     'nationality_id',
    //     'email',
    //     'secondary_email',
    //     'phone',
    //     'secondary_phone',
    //     'sector_id',
    //     'business_type',
    //     'password',
    // ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * many-to-many through relationship - locations where business/company conducts it's operations.
     */
    public function locations()
    {
        return $this->belongsToMany(SelectableCountry::class,
            "user_locations",
            "user_id",
            "location_id"
        );
    }

    public function chat_rooms()
    {
        return $this->belongsToMany(ChatRoom::class);
    }

    public function reviews()
    {
        return $this->hasMany(BusinessReview::class);
    }

    public function getEmailAttribute(){
        return $this->business->email;
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->slug = Str::slug($user->full_name);
            // $post->update(['slug' => $post->title]);
        });
    }

    public function getIsBusinessLikedAttribute($business_id){
        return $this->businessLikeDislike()->where(["is_like"=> true, "business_id"=>$business_id])->exists();
    }

    public function getIsBusinessDisikedAttribute($business_id){
        return $this->businessLikeDislike()->where(["is_like"=> false, "business_id"=>$business_id])->exists();
    }

    public function getIsBusinessSavedAttribute($business_id){
        return $this->savedBusinesses()->where("business_id", $business_id)->exists();
    }

    public function getIsBusinessFollowedAttribute($business_id){
        return $this->followedBusinesses()->where("business_id", $business_id)->exists();
    }

    public function business(){
        return $this->hasOne(Business::class);
    }

    public function teamBusiness(){
        return $this->belongsTo(Business::class);
    }

    public function conversations(){
        return $this->belongsToMany(Conversation::class);
    }

    public function businessRole(){
        return $this->belongsTo(SelectableBusinessRole::class, "business_role_id");
    }

    public function investorType(){
        return $this->belongsTo(SelectableInvestorType::class, "investor_type_id");
    }

    public function businessLikeDislike(){
        return $this->belongsToMany(Business::class, "user_business_like_dislike")->withTimestamps();
    }

    public function savedBusinesses(){
        return $this->belongsToMany(Business::class, "user_business_save")->withTimestamps();
    }

    public function followedBusinesses(){
        return $this->belongsToMany(Business::class, "user_business_follow")->withTimestamps();
    }

    public function callToActions(){
        return $this->hasMany(CallToAction::class);
    }

    public function wallet(){
        return $this->hasOne(Wallet::class);
    }

    public function savedProducts(){
        return $this->belongsToMany(Product::class, "saved_product_user")->withTimestamps();
    }

    public function getIsProductSavedAttribute($product_id){
        return $this->savedProducts()->where("product_id", $product_id)->exists();
    }

    public function Cart(){
        return $this->hasOne(Cart::class);
    }

    public function deliveryAddresses(){
        return $this->hasMany(DeliveryAddress::class);
    }

    public function viewedBusinessProfiles(){
        return $this->belongsToMany(Business::class, "profile_views")->withTimestamps();
    }

    public function Support()
    {
        return $this->hasMany(Support::class);
    }
}
