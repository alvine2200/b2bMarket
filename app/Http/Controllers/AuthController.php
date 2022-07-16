<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Business;
use App\Http\Resources\User as UserResource;
use App\Rules\MatchOldPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    //
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), 400);
        }

        $business = Business::where('email',$request->email)->first();
        if($business != null){
            $user = $business->user;
            $token = Auth::attempt(['id'=>$user->id, 'password'=>$request->password]);
        }
        else{
            $token = false;
        }

        if(!$token){
            $user = User::where('admin_email', $request->email)->first();
            if($user != null){
                $token = Auth::attempt(['id'=>$user->id, 'password'=>$request->password]);
            }
        }

        if(! $token){
            return $this->sendError('Incorrect email or password. Please try again.', ['error'=>'Unauthorised'], 401);
        }

        return $this->sendResponse($this->createNewToken($token), "Login successful");
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(){
        Auth::logout();
        return $this->sendResponse([], "User successfully logged out");
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->sendResponse($this->createNewToken(Auth::refresh()), "Token refreshed successfully");
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        return $this->sendResponse(Auth::user(), "Profile retrieved successfully");
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in_seconds' => Auth::factory()->getTTL() * 60,
            'user' => Auth::user()
        ];
    }

    public function changePassword(Request $request)
    {
        $messages = ['new_password.regex' => 'The :attribute contain at least 6 characters, a letter, a symbol and a number.',];
        $passwordRegex = "/^.*(?=.{6,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#@%]).*$/";

        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', "regex:$passwordRegex"],
            'new_confirm_password' => ['same:new_password'],
        ], $messages);

        $user = User::find(auth()->user()->id);
        if(!Hash::check($request->current_password, auth()->user()->password)){
            return $this->sendError('Error validation', ["current_password" =>"invalid current_password"], 400);
        }

        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);

        return $this->sendResponse([], "Password change successful.");
    }
}
