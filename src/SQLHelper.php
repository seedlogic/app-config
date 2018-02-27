<?php
/**
 * @file SQLHelper.php
 * @Brief: A simple PDO connection class to allow for portability
 **/
namespace Seedlogic\Utils\AppConfig;

use \PDO;
use \PDOException;
use Seedlogic\Utils\AppConfig\EnvironmentHelper;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class SQLHelper extends PDO
{
	/**
	 * DB Host value
	 * 
	 * @access private
	 * @var string
	 **/
	private $host;
	
	/**
	 * DB Database name value
	 * 
	 * @access private
	 * @var string
	 **/
	private $database;
	
	/**
	 * DB Username value
	 * 
	 * @access private
	 * @var string
	 **/
	private $user;
	
	/**
	 * DB Password value
	 * 
	 * @access private
	 * @var string
	 **/
	private $pass;
	
	/**
	 * Monolog instance for logging & error handling.
	 * 
	 * @access private
	 * @var Logger
	 **/
	private $logger;
	
	/**
	 * Object Constructor.
	 * 
	 * If specified, connection params can be passed here to be used during the connect()
	 * method, otherwise connection defaults to defined values in config.
	 *
	 * @param string $host	The host location (default is 'localhost').
	 * @param string $db	The database name value.
	 * @param string $user	The username value.
	 * @param string $pass	The password string.
	 * 
	 * @return SQLHelper
	 **/
	public function __construct($host=null, $db=null, $user=null, $pass=null)
	{
		$this->logger = new Logger('PDO_Log');
		$this->logger->pushHandler(new StreamHandler('../../logs/PDO_Error.log', Logger::ERROR));

		if(isset($host) && isset($db) && isset($user) && isset($pass))
		{
			$this->host = $host;
			$this->database = $db;
			$this->user = $user;
			$this->pass = $pass;
		}
		elseif(isset($db) && isset($user) && isset($pass))
		{
			if(extension_loaded('apc'))
			{
				apc_load_constants('db');
			}

			$this->host = CONNECTION_HOST;
			$this->database = $db;
			$this->user = $user;
			$this->pass = $pass;
		}
		else
		{
			if(extension_loaded('apc'))
			{
				apc_load_constants('db');
			}

			$this->host = CONNECTION_HOST;
			$this->database = CONNECTION_DATABASE;
			$this->user = CONNECTION_USER;
			$this->pass = CONNECTION_PASSWORD;
		}

		return $this;
	}
	
	/**
	 * @Brief: Creates a PDO MySQL connection.
	 * 
	 * @access public
	 * @return PDO|void
	 **/
	public function connect() 
	{
		try
		{
			// DB Connection
			$pdo = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->database, $this->user, $this->pass, array(
				PDO::ATTR_PERSISTENT => true
			));
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$pdo->exec("SET CHARACTER SET utf8");

			$this->logger->info('PDO connection established');
			
			return $pdo;
		}
		catch(PDOException $e)
		{
			$this->logger->alert('PDO SQL Connection Failure: ' . $e->getMessage());
		}
	}
	
	/**
	 * @Brief: Escapes a text string for inputting into a DB, or using in data validation.
	 * 
	 * @access public
	 * @param string $input: Input string to be cleaned.
	 * 
	 * @return string
	 **/
	static public function clean($input)
	{
		return "'" . @trim(addslashes(strip_tags($input))) . "'";
	}
}