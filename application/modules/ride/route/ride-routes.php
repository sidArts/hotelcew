<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$route['products/'] 				= "products/index";

$route['admincp/ride/all'] 		                    = "ride/admin/User/list";
$route['admincp/ride/global-rate'] 		                    = "ride/admin/User/global_rate";
$route['admincp/ride/create'] 		                    = "ride/admin/User/create_ride";
$route['admincp/ride/update/(:any)'] 		            = "ride/admin/User/create_ride/";
$route['admincp/user/ride/save'] 		                    = "ride/admin/User/save_ride";
$route['admincp/user/ride/list'] 		                    = "ride/admin/User/ridelist";
$route['admincp/users/ride/remove']                          = "ride/admin/User/delete_ride";
$route['admincp/users/ride/checkout']                          = "ride/admin/User/checkout";
$route['admincp/users/ride/booking_details']                   = "ride/admin/User/booking_details";
$route['admincp/users/ride/cancelBooking']                   = "ride/admin/User/cancelBooking";
