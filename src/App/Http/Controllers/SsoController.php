<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class SsoController extends Controller
{
    public function logout(Request $request)
	{
		$request->session()->invalidate();
		$request->session()->regenerate();

        return redirect(request()->getScheme() . '://' . config('sso.url'));
	}

    public function callback(Request $request)
    {
        Session::flush();

        // Retrieve the authorization code from the query string
        $code = $request->query('code');

        if (!$code) {
            return response()->json(['error' => 'No authorization code provided'], 400);
        }

        try {
            // Make the POST request using Laravel's HTTP facade
            $response = Http::post(request()->getScheme() . '://' . config('sso.request_url') . '/oauth/token', [
                'grant_type'        => 'authorization_code',
                'client_id'         => config('sso.client_id'),
                'client_secret'     => config('sso.client_secret'),
                'redirect_uri'      => config('sso.redirect_uri'),
                'code'              => $code,
            ]);

            // Check if the request was successful
            if (!$response->successful()) {
                return response()->json(['error' => $response->body()], $response->status());
            }

            // Decode the JSON response body
            $responseData = $response->json();

            // Extract custom data
            $responseCustomData = $responseData['custom_data'] ?? null;

            if (!$responseCustomData || !isset($responseCustomData['user'])) {
                return response()->json(['error' => 'Invalid response from SSO server.'], 500);
            }

            session(['user-session' => $responseData]);

            return redirect()->route(config('sso.route_home'));
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
