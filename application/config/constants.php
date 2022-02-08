<?php



defined('BASEPATH') OR exit('No direct script access allowed');







/*



|--------------------------------------------------------------------------



| Display Debug backtrace



|--------------------------------------------------------------------------



|



| If set to TRUE, a backtrace will be displayed along with php errors. If



| error_reporting is disabled, the backtrace will not display, regardless



| of this setting



|



*/



defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);







/*



|--------------------------------------------------------------------------



| File and Directory Modes



|--------------------------------------------------------------------------



|



| These prefs are used when checking and setting modes when working



| with the file system.  The defaults are fine on servers with proper



| security, but you may wish (or even need) to change the values in



| certain environments (Apache running a separate process for each



| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should



| always be used to set the mode correctly.



|



*/



defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);



defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);



defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);



defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);







/*



|--------------------------------------------------------------------------



| File Stream Modes



|--------------------------------------------------------------------------



|



| These modes are used when working with fopen()/popen()



|



*/



defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');



defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');



defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care



defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care



defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');



defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');



defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');



defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');







/*



|--------------------------------------------------------------------------



| Exit Status Codes



|--------------------------------------------------------------------------



|



| Used to indicate the conditions under which the script is exit()ing.



| While there is no universal standard for error codes, there are some



| broad conventions.  Three such conventions are mentioned below, for



| those who wish to make use of them.  The CodeIgniter defaults were



| chosen for the least overlap with these conventions, while still



| leaving room for others to be defined in future versions and user



| applications.



|



| The three main conventions used for determining exit status codes



| are as follows:



|



|    Standard C/C++ Library (stdlibc):



|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html



|       (This link also contains other GNU-specific conventions)



|    BSD sysexits.h:



|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits



|    Bash scripting:



|       http://tldp.org/LDP/abs/html/exitcodes.html



|



*/



defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors



defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error



defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error



defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found



defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class



defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member



defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input



defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error



defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code



defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code











///////////////////////////////// CUSTOM CODE //////////////////////////////////////////







define('STATUS', ['A' => 'Active', 'I' => 'Inactive', 'D' => 'Deleted']);



define('RECOMENDED_PRODUCT', [0 => 'Active', 1 => 'Inactive']);



define('POPULAR_PRODUCT', [0 => 'Active', 1 => 'Inactive']);



define('SITE_NAME', "Hotel Aviana");



define('DB_PREFIX', "st_");



define('REC_PER_PAGE', 10);



define('CURRENCY', '$');







define('DOMAIN_MAIL', "qd58xk6sqeph@hotelaviana.com");



define('ADMIN_MAIL',  "sankarnandi1010@gmail.com");


define('MAX_ADVANCE_BOOKING_IN_DAYS',  60);










/* ================= KEYS FOR LOCAL =====================*/



#define('CAPTCHA_SITE_KEY', "6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI");



#define('CAPTCHA_SECRET_KEY', "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe");



/* ================= KEYS FOR LOCAL =====================*/







/* ================= KEYS FOR IVAN DEV. SERVER =====================*/



define('CAPTCHA_SITE_KEY', "6Lemp1UUAAAAAPFMSFKiOfQB9qUo1-DqgF__-WLg");



define('CAPTCHA_SECRET_KEY', "6Lemp1UUAAAAALrJT1nWhJsStyXZs8KviBEY8Z5y");



/* ================= KEYS FOR IVAN DEV. SERVER =====================*/







/* ============== SMS GATEWAY ==============*/



#define('SMS_AUTH_KEY', "d7b83732-ada2-11e7-94da-0200cd936042");



#define('COUNTRY_CODE', "91");



/* ============== SMS GATEWAY ==============*/







$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";	



$root  = $protocol.$_SERVER['HTTP_HOST'];



define('CURRENT_PAGE', $root.$_SERVER['REQUEST_URI']);



$root .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);



define('BASE_URL', $root);



define('ADMIN_URL', $root."admincp/");



define('PUBLIC_PATH', $root."public/");



define('ADMIN_CSS', PUBLIC_PATH.'backend/assets/css/');



define('ADMIN_JS', PUBLIC_PATH.'backend/assets/js/');



define('ADMIN_PLUGINS', PUBLIC_PATH.'backend/assets/plugins/');



define('FRONT_CSS', PUBLIC_PATH.'assets/front/css/');



define('FRONT_ASSETS', PUBLIC_PATH.'assets/front/');











define('DEFAULT_IMAGE', 		PUBLIC_PATH.'default/image.png');



define('UPLOAD_PATH', 			BASE_URL.'public/uploads/');



define('UPLOADS_REAL_PATH', 	$_SERVER['DOCUMENT_ROOT'].parse_url(UPLOAD_PATH, PHP_URL_PATH));







/* =============== LOGING SESSIONS ===============*/



define('CUSTOMER_SESS', 		"customer_sess");



define('ADMIN_SESS', 			"admin_sess");















/* ==================== Tables ======================== */



define('CONTENTS', 						DB_PREFIX."contents");



define('CONTENT_TYPE', 					DB_PREFIX."content_type");



define('USER_ROLES', 					DB_PREFIX."user_roles");



define('USERS', 						DB_PREFIX."users");



define('USER_PROFILES', 				DB_PREFIX."user_profiles");



define('ADDRESS', 						DB_PREFIX."address");



define('BULK_PRODUCT_PRICES', 			DB_PREFIX."bulk_product_prices");



define('CART_ITEMS', 					DB_PREFIX."cart_items");



define('COUPONS', 						DB_PREFIX."coupons");



define('COUPON_PRODUCTS', 				DB_PREFIX."coupon_products");



define('COUPON_USERS', 					DB_PREFIX."coupon_users");



define('COUPON_USE_LOGS', 				DB_PREFIX."coupon_use_logs");



define('IMAGES', 						DB_PREFIX."images");



define('INDUSTRIES', 					DB_PREFIX."industries");



define('ORDERS', 						DB_PREFIX."orders");



define('ORDER_ACTIONS', 				DB_PREFIX."order_actions");



define('ORDER_ACTION_LOGS', 			DB_PREFIX."order_action_logs");



define('ORDER_ITEMS', 					DB_PREFIX."order_items");



define('PRODUCTS', 						DB_PREFIX."products");



define('PRODUCT_ATTRIBUTES', 			DB_PREFIX."product_attributes");



define('PRODUCT_CATEGORIES', 			DB_PREFIX."product_categories");



define('PRODUCT_IMAGES', 				DB_PREFIX."product_images");



define('PRODUCT_INDUSTRIES', 			DB_PREFIX."product_industries");



define('PRODUCT_SKUS', 					DB_PREFIX."product_skus");



define('PRODUCT_SKU_VALUES', 			DB_PREFIX."product_sku_values");



define('VARIANTS', 						DB_PREFIX."variants");



define('RECENTLY_VIEWED_PRODUCTS', 		DB_PREFIX."recently_viewed_products");



define('RELATED_PRODUCTS', 				DB_PREFIX."related_products");



define('SETTINGS', 						DB_PREFIX."settings");



define('TRANSACTIONS', 					DB_PREFIX."transactions");



define('WISHLIST', 						DB_PREFIX."wishlist");



//NEW TABLES

define('ROOM', 					        DB_PREFIX."hotel");


// Hotel booking special status constants

define('UNABLE_TO_CONTACT_CUSTOMER', 3);
define('CHECKIN_CANCELLED', 6);
define('CHECKOUT', 8);
define('CANCELLED', 9);

// Special status id list
define('SPECIAL_BOOKING_STATUS_LIST', [
	UNABLE_TO_CONTACT_CUSTOMER,
    CHECKIN_CANCELLED,
    CHECKOUT,
    CANCELLED
]);








