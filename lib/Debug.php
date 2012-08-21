<?php
namespace Phud;

class Debug
{
	public static function log($msg)
	{
		$fp = fopen(__DIR__.'/../debug.log', 'a');
		fwrite($fp, date('Y-m-d H:i:s')." ".$msg."\n");
		fclose($fp);
	}
}
