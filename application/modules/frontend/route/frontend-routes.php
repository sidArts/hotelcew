<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//$route['products/'] 				= "products/index";

$route['home'] 		                    = "frontend/Home/index";
$route['contact-us']            = "frontend/contactUs/";
$route['rates']            = "frontend/rates/";
$route['gallery']            = "frontend/gallery/";
$route['thank-you']            = "frontend/thankyou/";
$route['rooms/(:any)'] = 'frontend/roomDetails/$1';
$route['frontend/createbooking']            = "frontend/createBooking/";
// $route['admincp/ride/global-rate'] 		                    = "ride/admin/User/global_rate";
// $route['admincp/ride/create'] 		                    = "ride/admin/User/create_ride";
// $route['admincp/ride/update/(:any)'] 		            = "ride/admin/User/create_ride/";
// $route['admincp/user/ride/save'] 		                    = "ride/admin/User/save_ride";
// $route['admincp/user/ride/list'] 		                    = "ride/admin/User/ridelist";
// $route['admincp/users/ride/remove']                          = "ride/admin/User/delete_ride";
// $route['admincp/users/ride/slot/remove']                     = "ride/admin/User/delete_slot";
// $route['admincp/users/ride/slot/update']                     = "ride/admin/User/update_slot";
