<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';

$route['auth/register']['POST'] = 'auth/submitregistration';
$route['auth/login']['POST'] = 'auth/getlogin';

$route['refreshtoken/(:any)']['GET'] = 'waController/refreshToken/$1';
$route['single/(:any)/(:any)/(:any)']['GET'] = 'waController/single/$1/$2/$3';
$route['bulk/(:any)']['GET'] = 'waController/bulkSending/$1';
$route['otp/(:any)/(:any)']['GET'] = 'waController/otp/$1/$2';
$route['single-template']['GET'] = 'waController/singleTemplate';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;