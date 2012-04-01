<?php
namespace Phud;
class Debug
{
	
	private static $enabled = true;

	public static function clearLog()
	{
		if(!self::$enabled) {
			return;
		}
		global $global_path;
		
		$fp = fopen($global_path.'/debug.log', 'w');
		fwrite($fp, 'Truncated log, new log starting ' . date('Y-m-d H:i:s') . "\n");
		fclose($fp);
	}
	
	public static function log($msg)
	{
		if(!self::$enabled) {
			return;
		}
		global $global_path;
		
		$n = 0;
		if(Server::instance()) {
			$n = sizeof(Server::instance()->getClients());
		}
		$fp = fopen($global_path.'/debug.log', 'a');
		fwrite($fp, date('Y-m-d H:i:s')." ".$msg." [mem: ".(memory_get_usage(true)/1024)."kb, clients: ".$n."]\n");
		fclose($fp);
	}
}
?>
