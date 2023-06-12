<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SsoController extends Controller
{
    public function logout(Request $request)
	{
		$request->session()->flush();

		return redirect()->route('home');
	}
}
