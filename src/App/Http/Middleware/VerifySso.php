<?php

namespace App\Http\Middleware;

use App\Services\Support\CustomGate;
use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Auth\GenericUser;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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
			if(!$request->session()->has('user-session')){
				$key = config('sso.jwt_key');
				$jwt = $request->jwt;

				$decode = JWT::decode($jwt, new Key($key, 'HS256'));
				$response = Http::withToken($decode->token)
							->withHeaders([
								'Accept' => 'application/json'
							])
							->post(config('sso.url') . '/api/check-session', [
								'session_id' => $decode->session_id,
								'app_code' => $decode->app_code,
							]);

				$payload = json_decode($response->getBody());

				if(!$payload->success){
					throw new Exception('Session tidak valid');
				}

				$request->session()->put('user-session', $payload->data);
				setcookie('X-SSO-JWT', $decode->token);
				
			}else{

				$response = Http::withToken($_COOKIE['X-SSO-JWT'])
							->withHeaders([
								'Accept' => 'application/json'
							])
							->post(config('sso.url') . '/api/session-active', [
								'session_id' => $request->session()->get('user-session')->session_id,
							]);

				$payload = json_decode($response->getBody());

				if(!$payload->success){
					throw new Exception('Session tidak valid');
				}
			}
			$userSession = collect($request->session()->get('user-session'));

			$user = new GenericUser($userSession->toArray());
			auth()->setUser($user);

			foreach($request->session()->get('user-session')->permissions as $permission){
				Gate::define($permission->permission_name, function($user){
					return true;
				});
			}

			return $next($request);
		} catch (\Throwable $th) {
			$request->session()->flush();
			return redirect(config('sso.url'));
		}
    }
}

