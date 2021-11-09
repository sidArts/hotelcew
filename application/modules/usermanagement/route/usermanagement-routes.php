<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['admincp/user/all']                             = "usermanagement/admin/User/list";
$route['admincp/user/create']                             = "usermanagement/admin/User/createuser";
$route['admincp/user/update/(:any)']                     = "usermanagement/admin/User/createuser/";
$route['admincp/usermanagement/submit']                         = "usermanagement/admin/User/submituser";
$route['admincp/usermanagement/checkexsitingmail']                 = "usermanagement/admin/User/emailexsists_user_mail";
$route['admincp/usermanagement/list/data']                         = "usermanagement/admin/User/userlist";
$route['admincp/usermanagement/list/delete']                     = "usermanagement/admin/User/delete_user";
