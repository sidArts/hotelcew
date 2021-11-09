<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['admincp/admin-login']                     = "admincp/check_admin_login";
$route['admincp/settings/general']                 = "admincp/general_settings";
$route['admincp/general_settings_post']         = "admincp/general_settings_post";

$route['admincp/create/submit-user']            = "user/submitstoretypeuser/";

$route['admincp/admin-logout']                     = "admincp/admin_logout";
$route['admincp/stores/create']                   = "user/create_user/";

$route['admincp/stores/update/(:any)']                   = "user/create_user/";

$route['admincp/stores/all']                         = "user/roles/";
$route['admincp/users/stores']                     = "user/store_users/";
$route['admincp/users/remove']                     = "user/delete_user/";
$route['admincp/users/opcodecheck/']             = "user/checkopcode/";
$route['admincp/users/state']                     = "user/state/";
$route['admincp/users/cities']                     = "user/cities/";
$route['admincp/users/email']                     = "user/emailexsists/";


$route['admincp/changepassword']            = "admincp/changePassword/";
$route['admincp/gallery/all']                         = "user/gallery/";
$route['admincp/gallery/create']                   = "user/create_gallery/";

$route['admincp/site-settings']                   = "user/site_settings/";
$route['admincp/pages/(:any)']                   = "user/pages/$1";
$route['admincp/hotel-image/(:any)']                   = "user/hotel_image/$1";







