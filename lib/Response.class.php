<?php

class ResponseException extends Exception {}

/**
 * Build the response array that gets sent as a JSON response.
 *
 * Array contents:
 * code = Return code, maps to HTTP codes
 * message = User friendly message
 * data = Array of data, contents is op specific
 *
 */
class Response
{

public static function data(int $code, string $message, $data=null)
{
return array(
	'version'=>1,
	'code'=>$code,
	'message'=>$message,
	'data'=>$data);
}

public static function send(int $code, string $message, $data=null)
{
$r=Flight::request()->query;
$f=isset($r['f']) ? $r['f'] : 'json';
switch ($f) {
	case 'csv':
		Response::csv($code, $data);
	break;
	case 'json':
		Response::json($code, $message, $data);
	default:
	break;
}
return false;
}

public static function json(int $code, string $message, $data=null)
{
Flight::json(Response::data($code, $message, $data), $code);
return false;
}

public static function csv(int $code, array $data=null)
{
header('Content-type: text/csv');
header('Content-disposition: attachment;filename=sensor-data.csv');

if (count($data)==0)
	return false;

$out=fopen('php://output', 'w');
fputcsv($out, array_keys($data[0]));
foreach ($data as $r) {
	fputcsv($out, $r);
}
fclose($out);
return false;
}

public static function error(int $code, string $error)
{
return Response::data($code, $error);
}

} // class
