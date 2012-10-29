<?php
// Generated October 29, 2012 14:14:07

if (!defined('_VALID_'))
	die ('Unauthorized Access');

// Database Login
define('DB_NAME', 'invoice');
define('DB_USER', 'root');
define('DB_PASS', '123456');
define('DB_HOST', 'localhost');
define('DB_TBL_PRE', '');

// Company, Website, Admin and Misc
define('COMPANY_NAME', 'Asoft');
define('WEBSITE_PATH', '/clivo');
define('SECURITY_WORD', 'zxcvfdsa'); // Used to help encrypt invoice hash
define('ADMIN_EMAIL', 'winazerty@gmail.com');
define('CLIVO_VERSION', '2.5.1');

// Credits
define('CREDITS_NAME', 'Credits');

// Authorize.net Credentials
define('AUTH_LOGIN', '');
define('AUTH_TRANS_KEY', '');
define('AUTH_TEST', 'TRUE');

// Paypal
define('PAYPAL_EMAIL', '');
define('PAYPAL_TEST', 'TRUE');

?>