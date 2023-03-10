<?php

/**
 * Handle sensor data requests
 *
 */
class SensorHandler Extends Handler
{
private $db;

private function db()
{
try {
$this->db=new PDO($this->c['Database']['dsn'],
	$this->c['Database']['username'],
	$this->c['Database']['password']);
} catch (PDOException $e) {
	Response::json(500, 'Database failure');
}
}

function getSensors()
{
$this->db();
try {
	$stmt=$this->db->prepare('select sensor,max(dt) as ts from airquality group by sensor');
	$r=$stmt->execute();
	$result=$stmt->fetchAll(PDO::FETCH_ASSOC);
	Response::json(200, count($result), $result);
} catch (PDOException $e) {
	Response::json(500, 'Failed to get sensors');
}
}

function getSensorData(?string $id=null, ?string $day=null)
{
$this->db();
try {
	if (is_string($day))
		$dt=\DateTime::createFromFormat('Ymd|', $day);
	else
		$dt=new DateTime();

	$sdt=$dt->format('Y-m-d');

	if (is_string($id)) {
		$stmt=$this->db->prepare('select * from airquality where sensor=? and dt::DATE=? order by sensor,dt');
		$stmt->bindParam(1, $id);
		$stmt->bindParam(2, $sdt);
	} else {
		$stmt=$this->db->prepare('select * from airquality where dt::DATE=? order by sensor,dt');
		$stmt->bindParam(1, $sdt);
	}

	$r=$stmt->execute();
	$result=$stmt->fetchAll(PDO::FETCH_ASSOC);

	Response::send(200, count($result), $result);
} catch (PDOException $e) {
	Response::json(500, 'Failed to get sensor data');
}
}

} // class
