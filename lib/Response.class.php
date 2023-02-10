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

public static function data($code, $message, $op, $data=null)
{
return array(
	'version'=>1,
	'code'=>$code,
	'message'=>$message,
	'data'=>$data);
}

public static function json($code, $message, $data=null)
{
Flight::json(Response::data($code, $message, null, $data), $code);
return false;
}

public static function error($code, $error, $op)
{
return Response::data($code, $error, $op);
}

} // class
