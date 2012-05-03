<?php
namespace Phud;
use Phud\Actors\Actor,
	Phud\Actors\Mob;

class Debug
{
	
	private static $enabled = true;

	public static function start()
	{
		if(!self::$enabled) {
			return;
		}
		Server::instance()->on('tick', function($event, $server) {
			Debug::log(
				"\ntick status update\n".
				"==========================================\n".
				"rooms                       ".sizeof(Room::getAll())."\n".
				"mobs                        ".Mob::getCounter()."\n".
				"clients                     ".sizeof($server->getClients())."\n".
				"memory                      ".(memory_get_peak_usage(true)/1024)." kb\n".
				"allocated                   ".(memory_get_usage(true)/1024)." kb");
		});
	}

	public static function log($msg)
	{
		if(!self::$enabled) {
			return;
		}
		global $global_path;
		$fp = fopen($global_path.'/debug.log', 'a');
		fwrite($fp, date('Y-m-d H:i:s')." ".$msg."\n");
		fclose($fp);
	}
}
?>
