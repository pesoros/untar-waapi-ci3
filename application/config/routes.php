<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';

$route['auth/register']['POST'] = 'auth/submitregistration';
$route['auth/login']['POST'] = 'auth/getlogin';

$route['wa/refreshtoken/(:any)']['GET'] = 'waController/refreshToken/$1';
$route['wa/single/(:any)/(:any)/(:any)']['GET'] = 'waController/single/$1/$2/$3';
$route['wa/bulk/(:any)']['GET'] = 'waController/bulkSending/$1';
$route['wa/otp/(:any)/(:any)']['GET'] = 'waController/otp/$1/$2';
$route['wa/single-template']['GET'] = 'waController/singleTemplate';
$route['exporttest']['GET'] = 'waController/exportCsv';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;