<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
|--------------------------------------------------------------------------
| Version of system
|--------------------------------------------------------------------------
|
| Definition of system version
| to control when new features and bugfixes are released
| and to avoid CSS conflicts on mobiles devices
|
| Format: YYYY.S.R
|--------------------
| YYYY  - current year
| S     - current semester of year (1st or 2nd)
| R     - current release version of system
|
*/
define('VERSION_APP', '2023.2.45');

// previsão de lançamento do modulo de Despesas (segundo semestre de 2023)
// define('VERSION_APP', '2023.2.?'); 

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
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 'rb');
define('FOPEN_READ_WRITE', 'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 'ab');
define('FOPEN_READ_WRITE_CREATE', 'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/* End of file constants.php */
/* Location: ./application/config/constants.php */
