<?php
namespace Phud;

class Debug
{
	protected static $instance = null;

	public function instance()
	{
		return self::$instance ? self::$instance : self::$instance = new self();
	}

	public static function log($message)
	{
		self::write('info', $message);
	}

	public static function warn($message)
	{
		self::write('warn', $message);
	}


	public static function error($message)
	{
		self::write('error', $message);
	}

	protected static function write($level, $message)
	{
		$fp = fopen(__DIR__.'/../debug.log', 'a');
		fwrite($fp, date('Y-m-d H:i:s')." [".$level."] ".$message."\r\n");
		fclose($fp);
	}
}
