<?php

namespace App\Http\Middleware;

use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;

class VerifySso
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            if (!$request->session()->has('user-session')) {
                return redirect()->route('sso.auth');
            }

            $responseSession = collect($request->session()->get('user-session'));

            $issuedAt = Carbon::parse($responseSession['custom_data']['issued_at']);
            $expiresIn = $responseSession['expires_in']; // in seconds
            $expiredAt = $issuedAt->addSeconds($expiresIn);

            $userSession = $responseSession['custom_data']['user'];

            $genericUser = new GenericUser($userSession);

            $user = new User();

            $user->user_id = $genericUser->user_id;
            $user->name = $genericUser->name;
            $user->email = $genericUser->email;
            $user->session_id = $genericUser->session_id;
            $user->role = $genericUser->role;
            $user->all_store = $genericUser->all_store;

            $user->working_area = collect($genericUser->working_area)->map(function ($area) {
                $area['roles'] = collect($area['roles'])->map(function ($role) {
                    return (object) $role;
                })->all();
                $area['departments'] = collect($area['departments'])->map(function ($department) {
                    return (object) $department;
                })->all();
                return (object) $area;
            })->all();

            $user->stores = collect($genericUser->stores)->map(function ($store) {
                return (object) $store;
            })->all();
            $user->permissions = $genericUser->permissions;
            $user->credentials = [
                'token_type' => $responseSession['token_type'],
                'access_token' => $responseSession['access_token'],
                'refresh_token' => $responseSession['refresh_token'],
                'expired_at' => $expiredAt
            ];

            auth()->setUser($user);

            foreach ($request->session()->get('user-session')['custom_data']['user']['permissions'] as $permission) {
                Gate::define($permission['permission_name'], function ($user) {
                    return true;
                });
            }

            $user = auth()->user();
            $accessToken = $user->credentials['access_token'];

            if ($user->credentials['expired_at']->isPast()) {
                $response = Http::retry(5, 500)->asForm()->post(config('sso.api_url') . '/api/oauth/introspect', [
                    'token' => $accessToken,
                ]);

                $data = $response->json();
                if (!$data['active']) {
                    $this->refreshingToken($user);
                }
            }
            return $next($request);
        } catch (\Throwable $th) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect(config('sso.url'));
        }
    }

    public function refreshingToken($user)
    {
        $refreshToken = $user->credentials['refresh_token'];
        $refreshResponse = Http::retry(5, 500)->asForm()->post(config('sso.api_url') . '/oauth/token', [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => config('sso.client_id'),
            'client_secret' => config('sso.client_secret'),
            'scope' => '',
        ]);

        if ($refreshResponse->status() === 401) {
            Session::flush();
            return redirect()->route('sso.auth');
        }

        // Perbarui Access Token di User Session
        $newTokenData = $refreshResponse->json();

        session(['user-session' => $newTokenData]);

        // Perbarui data user
        $updatedCredentials = auth()->user()->credentials;
        $updatedCredentials['access_token'] = $newTokenData['access_token'];
        $updatedCredentials['refresh_token'] = $newTokenData['refresh_token'];
        $updatedCredentials['expires_in'] = $newTokenData['expires_in'];

        // Tetapkan kembali credentials
        $user->credentials = $updatedCredentials;

        // Simpan user baru ke sesi
        auth()->setUser($user);

        return $newTokenData;
    }
}
