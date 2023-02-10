<?php
/**
 * Response sensor API
 */

date_default_timezone_set('Europe/Helsinki');

// Enable for development only
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('vendor/autoload.php');

require_once('lib/Syslog.php');
require_once('lib/Response.class.php');
require_once('lib/Handler.class.php');
require_once('lib/SensorHandler.class.php');

/*****************************************************************************/

if (!file_exists("config.ini"))
	die('Configuration file config.ini is missing');

$config=parse_ini_file("config.ini", true);

function versionResponse() {
	Response::json(200, 'API Version 1', 'version', array('version'=>1));
	die();
}

$s=new SensorHandler($config);

Flight::route('GET /', 'versionResponse');
// Flight::route('GET /version', 'versionResponse');

/* Get list of sensor IDs in the system */
Flight::route('GET /sensors/', array($s, 'getSensors'));

/* Get latest values from all sensors for current day */
Flight::route('GET /sensor/', array($s, 'getSensorData'));

/* Get latest value for given sensor for current day */
Flight::route('GET /sensor/@id:[a-z0-9]{12}', array($s, 'getSensorData'));

/* Get all data from given sensor, for given day YYYYMMDD */
Flight::route('GET /sensor/@id:[a-z0-9]{12}/@day:[0-9]{4}[0-9]{2}[0-9]{2}', array($s, 'getSensorData'));

Flight::map('notFound', function() {
	Response::json(404, 'Not found');
	die();
});

Flight::map('error', function($e) {
	$c=$e->getCode();
	if ($c<400) $c=500;
	slog("Internal error exception", $e->getMessage(), $e, true);
	Response::json($c, "Internal system error", $e->getMessage());
});

Flight::start();
?>
