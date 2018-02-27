<?php
/**
 * Example config file.
 * 
 * Feel free to use this example config file as a bootstrap file with your 
 * application or simply ignore it and make your own. 
 * 
 **/

// Composer link-in
require_once DOC_ROOT . "/vendor/autoload.php";

use Seedlogic\Utils\AppConfig\EnvironmentHelper;
use Seedlogic\Utils\AppConfig\SQLHelper;

global $environment;
if(!$environment instanceof EnvironmentHelper) 
{
	$environment = new EnvironmentHelper('dev');
}

// Global DB connection variable
global $conn;

// Add anything else you need here.