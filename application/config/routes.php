<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';

$route['auth/register']['POST'] = 'auth/submitregistration';
$route['auth/login']['POST'] = 'auth/getlogin';

$route['wa/refreshtoken/(:any)']['GET'] = 'wa_services/refreshToken/$1';
$route['wa/single/(:any)/(:any)/(:any)']['GET'] = 'wa_services/single/$1/$2/$3';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


// sample route =>
// $route['product/(:any)'] = 'catalog/product_lookup';
// $route['product/(:num)'] = 'catalog/product_lookup_by_id/$1';