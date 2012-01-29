<?php
namespace Mechanics;
class Debug
{
	
	private static $enabled = true;

	public static function clearLog()
	{
		if(!self::$enabled)
			return;
		
		$fp = fopen('debug.log', 'w');
		fwrite($fp, 'Truncated log, new log starting ' . date('Y-m-d H:i:s') . "\n");
		fclose($fp);
	}
	
	public static function addDebugLine($msg, $new_line = true)
	{
		if(!self::$enabled)
			return;
		
		$fp = fopen('debug.log', 'a');
		if($new_line)
			fwrite($fp, date('Y-m-d H:i:s')." ".$msg." [mem: " . (memory_get_usage(true)/1024) . "kb, users: " . sizeof(\Living\User::getInstances()) . "]\n");
		else
			fwrite($fp, $msg);
		fclose($fp);
	}
}
?>
