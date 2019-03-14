<?
//
// include/config.inc.php
//
// configuration file


//
// MYSQL
//

// mysql server adress
define('DB_SERVER', 'localhost');
// database name
define('DB_NAME', '');
// mysql server login
define('DB_LOGIN', '');
// mysql server password
define('DB_PWD', '');


//
// ENVIRONMENT VARIABLES
//

// uri root path
define('ROOT_PATH', '');
// document root path
define('DOCUMENT_ROOT_PATH', $_SERVER["DOCUMENT_ROOT"].'');
// path to the plugin directory
define('TMPL_DIR', DOCUMENT_ROOT_PATH.'/templates');
// path to the dynamic image directory
define('DYN_IMG_DIR', DOCUMENT_ROOT_PATH.'/userimages');
/*// path to the admin template directory
define('ADMIN_TMPL_DIR', DOCUMENT_ROOT_PATH.'/admin/t');
// dynamic image directory uri
define('DYN_IMG_PATH', ROOT_PATH.'/d');*/

$GLOBALS['INSTALLED_LANG'] = array('de', 'en');
define('DEFAULT_LANG', 'en');


//
// RUNTIME VARIABLES
//

// display errors:
define('DEBUG_MODE', true);
// use gzip compression for document delivery
define('USE_GZIP', false);
?>
