<?php
/**
 * Example config file.
 * 
 * Feel free to use this example config file as a bootstrap file with your 
 * application or simply ignore it and make your own. 
 * 
 **/

// Composer link-in
require_once __DIR__ . "/../../../vendor/autoload.php";

use Seedlogic\Utils\AppConfig\EnvironmentHelper;
use Seedlogic\Utils\AppConfig\SQLHelper;

// Defining input/output protocols if they're not already defined
if(!defined('STDIN'))  define('STDIN',  fopen('php://stdin',  'r'));
if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'w'));
if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'w'));	

// Global environment instance variable
global $environment;
if(!$environment instanceof EnvironmentHelper) 
{
	$environment = new EnvironmentHelper('dev');
}

// Error Handling
error_reporting(E_STRICT | E_ALL);

// Global DB connection variable
global $conn;

// Add anything else you need here.