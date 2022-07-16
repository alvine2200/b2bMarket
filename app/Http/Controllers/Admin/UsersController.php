<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\UsersResource;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;

class UsersController extends BaseController
{
    public function get_users(){

       // if(Auth::user()->is_super_admin)
       // {
            $users=User::all();

            return $this->SendResponse(UsersResource::collection($users),'All users Shown');
       // }
    }

    public function get_investors()
    {
       // if(Auth::user()->is_super_admin)
        //{
            $investors=DB::table('users')
            ->select('full_name','business_role_id','gender_id','fb_id','nationality_id','sector_id','investor_type_id','is_business_rep','is_employee','profile_image','quote','is_team_member')
            ->where('is_investor',true)
            ->get();

            return $this->SendResponse(new UsersResource($investors),'Investors are listed Successfully');
      // }


    }

    public function get_african_business()
    {
        /*if(Auth::user()->is_admin)
        {  $user=DB::table('users')
            ->select(array('full_name','business_role_id','gender_id','fb_id','nationality_id','sector_id','investor_type_id','is_business_rep','is_employee','profile_image','quote','is_team_member'))
            ->whereHas('business',function($query){
                $query->where('business_type','African')->get();
        }); */

            $users=User::whereHas('business', function ($query){
                $query->where('business_type','African');
            })->get();

            return $this->SendResponse(UsersResource::collection($users),'All African Business Users Listed');
       // }

    }

    public function get_non_african_business()
    {
       /* if(Auth::user()->is_admin)
        {   $user=DB::table('users')
            ->select(array('full_name','business_role_id','gender_id','fb_id','nationality_id','sector_id','investor_type_id','is_business_rep','is_employee','profile_image','quote','is_team_member'))
            ->whereHas('business',function($query){
                $query->where('business_type','Non_African')
                ->get();
            });
            return $this->SendResponse(UsersResource::collection($user),'All African Business Users Listed');
        }  */

        $user=User::whereHas('business', function ($query){
            $query->where('business_type','Non-African');
        })->get();

        return $this->SendResponse(UsersResource::collection($user),'All Non-African Business Users Listed');

    }

}
