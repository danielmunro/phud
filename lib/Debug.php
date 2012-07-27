<?php
namespace Phud;

class Debug
{
	public static function log($msg)
	{
		global $global_path;
		$fp = fopen($global_path.'/debug.log', 'a');
		fwrite($fp, date('Y-m-d H:i:s')." ".$msg."\n");
		fclose($fp);
	}
}
