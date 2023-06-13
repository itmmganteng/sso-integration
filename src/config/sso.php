<?php

return [
	'jwt_key' => env('JWT_KEY'),
	'url' => env('SSO_URL', 'http://sso-mm.local.com'),
	'port' => env('SSO_PORT', '80'),
];