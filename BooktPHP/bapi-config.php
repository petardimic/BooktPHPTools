<?php
ini_set('error_reporting', E_ERROR);  //Use E_ERROR for production implementation, or E_ALL for debugging/development.
ini_set( 'default_charset', 'UTF-8' );
set_time_limit(300);

/* Fill in the following constant values during installation. */
define('API_KEY', ''); //Enter your Bookt API Key Here
define('SOLUTIONID', -1); //Enter your Bookt SolutionID Here
define('DB_LOCATION', 'localhost');
define('DB_NAME', 'dbname');
define('DB_USER', 'user');
define('DB_PASSWORD', 'pass');
define('DB_MODE', 'mysql'); //Possible values: 'mysql', 'mssql' - Not in use yet, will require 2 versions of many queries.  Seems impossible to connect w/ SQL Azure on Linux host, but normal SQL host in Azure is possible.

?>