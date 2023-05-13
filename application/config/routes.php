<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'welcome';

$route['auth/register']['POST'] = 'auth/submitregistration';
$route['auth/login']['POST'] = 'auth/getlogin';

$route['waservices/testdb']['GET'] = 'wa_services/testdbloc';

$route['waservices/refreshtoken']['GET'] = 'wa_services/refreshToken';
$route['waservices/send/single']['POST'] = 'wa_services/single';

$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


// sample route =>
// $route['product/(:any)'] = 'catalog/product_lookup';
// $route['product/(:num)'] = 'catalog/product_lookup_by_id/$1';