<?php
require_once(__DIR__.'/../autoloader.php');
require_once(__DIR__.'/../helpers.php');

(new \Phud\Deploy('lib', 'deploy'))->deployEnvironment(new \Phud\Server([
	'host' => '127.0.0.1',
	'port' => '9000',
]));

define('UNIT_TESTING', 1);
