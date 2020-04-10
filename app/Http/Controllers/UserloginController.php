<?php

namespace App\Http\Controllers;

use App\User;
use Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class UserloginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api',['except'=>['signup','login']]);
        
    }
   
   
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed',
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);
        $user->save();
        return response()->json([
            'message' => 'Successfully created user!',
        ], 201);
    }

     
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);



        $credentials = request(['email', 'password']);
        if (!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();

        $http = new Client;

        $response = $http->post(url("/oauth/token"), [
                    'form_params' => [
                        'grant_type'    => 'password',
                        'client_id'     => '2',
                        'client_secret' => '9SvV2mumkVu58mWRI4E13UQxxFU0489vjfGjs9on',
                        'username'      => request('email'),
                        'password'      => request('password'),
                        'provider'      => 'users',
                        'scope'         => '',
                    ],
                ]);
        $token =json_decode((string) $response->getBody(), true);

        
        
        return response()->json([
            'access_token' => $token['access_token'],
            'token_type' => 'Bearer',
            // 'expires_in' => Carbon::parse(
            //     $token['expires_in']
            // )->toDateTimeString(),
        ]);
        

    }

    
    public function logout(Request $request)
    {
        
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out',
        ]);
        
    }



}
