<?php

defined('BASEPATH') OR exit('No direct script access allowed');

$route['api/v1/test'] = "api/test"; # GET
//$route['api/v1/login'] = "api/api/login"; # GET
$route['api/v1/login'] = "api/auth/login"; # POST
$route['api/v1/ridelist'] = "api/api/ride_list"; # GET
$route['api/v1/createbooking'] = "api/api/createBooking"; # POST
$route['api/v1/cancel_ride'] = "api/api/cancel_ride"; # POST
$route['api/v1/ride_start'] = "api/api/ride_start"; # POST
$route['api/v1/ride_end'] = "api/api/ride_end"; # POST
$route['api/v1/booking_list'] = "api/api/booking_list"; # POST
$route['api/v1/booking_list_by_status'] = "api/api/booking_list_by_status"; # POST
$route['api/v1/ride_list_by_status'] = "api/api/ride_list_by_status"; # POST
$route['api/v1/ride_list_by_booking'] = "api/api/ride_list_by_booking"; # POST
$route['api/v1/forgotpass'] = "api/auth/forgotpass"; # POST
$route['api/v1/imageupload'] = "api/api/upload_image"; # POST
$route['api/v1/changepassword'] = "api/api/changepassword"; # POST
$route['api/v1/resetpassword'] = "api/auth/resetpassword"; # POST
