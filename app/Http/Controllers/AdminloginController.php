<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\AccessTokenController;

class AdminloginController extends Controller
{
    protected function guard()
    {
        return Auth::guard('api_admin');
    }

    public function __construct()
    {
        $this->middleware('auth:api_admin',['except'=>['login']]);
        
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|exists:admins,email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
        ]);



        $credentials = request(['email', 'password']);
        if (!Auth::guard('admin')->attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::guard('admin')->user();

        // $http = new Client;

        // $response = $http->post(url("/oauth/token"), [
        //             'form_params' => [
        //                 'grant_type'    => 'password',
        //                 'client_id'     => '2',
        //                 'client_secret' => '9SvV2mumkVu58mWRI4E13UQxxFU0489vjfGjs9on',
        //                 'username'      => request('email'),
        //                 'password'      => request('password'),
        //                 'provider'      => 'admins',
        //                 'scope'         => '',
        //             ],
        //         ]);
        // $token =json_decode((string) $response->getBody(), true);

        
        
        // return response()->json([
        //     'access_token' => $token['access_token'],
        //     'token_type' => 'Bearer',
        //     // 'expires_in' => Carbon::parse(
        //     //     $token['expires_in']
        //     // )->toDateTimeString(),
        // ]);

        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(4);
        }

        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
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
