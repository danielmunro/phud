<?php

	/**
	 *
	 * Phud - a PHP implementation of the popular multi-user dungeon game paradigm.
     * Copyright (C) 2009 Dan Munro
	 * 
     * This program is free software; you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation; either version 2 of the License, or
     * (at your option) any later version.
	 * 
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
	 * 
     * You should have received a copy of the GNU General Public License along
     * with this program; if not, write to the Free Software Foundation, Inc.,
     * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
	 *
	 * Contact Dan Munro at dan@danmunro.com
	 * @author Dan Munro
	 * @package Phud
	 *
	 */
	namespace Mechanics;
	class Debug
	{
		
		private static $enabled = true;
	
		public static function newLog()
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
			fwrite($fp, $msg . ($new_line === true ? " [mem: " . round(memory_get_usage(true)/1048576,2) . "]\n" : ''));
			fclose($fp);
		}
	}
?>
