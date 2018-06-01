<?php
/**
 * @file EnvrionmentHelper.php
 * EnvironmentHelper class. Used for various environment-specific 
 * operations and utilities.
 * 
 * @package Seedlogic\Utils;
 * @since v0.1
 **/
namespace Seedlogic\Utils\AppConfig;

/**
 * Class EnvironmentHelper
 **/
class EnvironmentHelper
{
	/**
	 * Current active environment
	 *
	 * @var string
	 **/
	private $active_environment;

	/**
	 * Retrives the current active environment
	 *
	 * @return string
	 **/
	public function get_active_environment()
	{
		return $this->active_environment;
	}

	/**
	 * Object constructor.
	 *
	 * If an environment name is passed in, it will set that environment as active,
	 * which can only be done once per session, or until the existing instance is
	 * either unset, or destroyed.
	 *
	 * @param string $enviro_name The name of the environment to initialize.
	 * @return EnvironmentHelper
	 * @throws UnexpectedValueException
	 **/
	public function __construct($enviro_name = null)
	{
		if(isset($enviro_name))
		{
			if(!empty($this->get_active_environment()))
			{
				throw new \UnexpectedValueException('Environment already set for this session');
			}
			else
			{
				$this->set_active_environment($enviro_name);
			}
		}

		return $this;
	}
	
	/**
	 * Sets the current active environment.
	 * 
	 * Defines the default environment email, default DB connection
	 * values, and active environment name.
	 *
	 * @param string $value The name of the environment being enabled.
	 * @return void
	 * @throws InvalidArgumentException
	 */
	public function set_active_environment($value)
	{
		// Set some properties for this object to return later
		$this->activeEnvironment = $value;

		if(!$appConfig = parse_ini_file(dirname(dirname(__FILE__)) . "/config/" . $value . "/appConfig.ini", true))
		{
			throw new \InvalidArgumentException("Unable to parse $value application configuration file");
		}

		// Define is just slow as balls, so we'll use APC to set defined constants if we can help it
		if($this->has_cache()) 
		{
			$constants = array(
				'db' => array (
					'CONNECTION_HOST'		=> $appConfig['db']['host'],
					'CONNECTION_USER'		=> $appConfig['db']['user'],
					'CONNECTION_PASSWORD'	=> $appConfig['db']['password'],
					'CONNECTION_DATABASE'	=> $appConfig['db']['database']
				),
				'paths' => array(
					'BASE_URL'	=> $appConfig['paths']['base_url'],
					'BASE_RSS'	=> $appConfig['paths']['base_rss'],
					'BASE_ATOM'	=> $appConfig['paths']['base_atom'],
					'BASE_API'	=> $appConfig['paths']['base_api'],
					'BASE_PATH'	=> $_SERVER['DOCUMENT_ROOT'] . $appConfig['paths']['base_path'],
				),
				'email' => array (
					'GLOBAL_DEFAULT'	=> $appConfig['email']['global'],
					'LOGGING_EMAIL'		=> $appConfig['email']['logging'],
					'FORMS_EMAIL'		=> $appConfig['email']['forms']
				)
			);

			apc_define_constants('db', $constants['db']);
			apc_define_constants('paths', $constants['paths']);
			apc_define_constants('email', $constants['email']);
		}
		else
		{
			define('CONNECTION_HOST', $appConfig['db']['host']);
			define('CONNECTION_USER', $appConfig['db']['user']);
			define('CONNECTION_PASSWORD', $appConfig['db']['password']);
			define('CONNECTION_DATABASE', $appConfig['db']['database']);

			define('BASE_URL', $appConfig['paths']['base_url']);
			define('BASE_RSS', $appConfig['paths']['base_rss']);
			define('BASE_ATOM', $appConfig['paths']['base_atom']);
			define('BASE_API', $appConfig['paths']['base_api']);
			define('BASE_PATH', $_SERVER['DOCUMENT_ROOT'] . $appConfig['paths']['base_path']);

			define('GLOBAL_DEFAULT', $appConfig['email']['global']);
			define('LOGGING_EMAIL', $appConfig['email']['logging']);
			define('FORMS_EMAIL', $appConfig['email']['forms']);
		}
	}

	/**
	 * Checks to see if the APC extension is installed.
	 *
	 * @return bool
	 **/
	public function has_cache()
	{
		return extension_loaded('apc');
	}
}