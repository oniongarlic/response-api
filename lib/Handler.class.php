<?php
/**
 * Request handler base class
 *
 * Common setup for most request handlers.
 * Includes frequently used helper methods.
 *
 * @package Handler
 */

class Handler
{
protected $c;

public function __construct(array $config=null)
{
$this->c=$config;
}

}
