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
	namespace Mechanics\Ability;
	use \Mechanics\Debug,
		\Mechanics\Actor,
		\Mechanics\Alias,
		\Mechanics\Event\Subscriber,
		\Mechanics\Event\Event,
		\ReflectionClass,
		\Exception;

	abstract class Ability
	{
		use Alias;

		protected $proficiency = '';
		protected $required_proficiency = 0;
		
		protected function __construct()
		{
			if(empty($this->proficiency) || empty($this->required_proficiency)) {
				throw new Exception(get_class($this)." is not configured with both a proficiency and a required level for that proficiency");
			}
		}

		public function getProficiency()
		{
			return $this->proficiency;
		}

		public static function runInstantiation()
		{
			$namespaces = ['Skills', 'Spells'];
			foreach($namespaces as $namespace) {
				$d = dir(dirname(__FILE__) . '/../../'.$namespace);
				while($ability = $d->read()) {
					if(substr($ability, -4) === ".php") {
						Debug::addDebugLine("init ability: ".$ability);
						$class = substr($ability, 0, strpos($ability, '.'));
						$called_class = $namespace.'\\'.$class;
						$reflection = new ReflectionClass($called_class);
						new $called_class();
					}
				}
			}
		}
	
		protected function getEasyAttributeModifier($attribute)
		{
			switch($attribute)
			{
				case ($attribute < 15):
					return rand(12, 17) / 100;
				case ($attribute < 17):
					return rand(8, 12) / 100;
				case ($attribute < 20):
					return rand(0, 6) / 100;
				case ($attribute < 22):
					return 0;
				case ($attribute < 25):
					return -(rand(0, 5) / 100);
				default:
					return -(rand(0, 10) / 100);
			}
		}
		
		protected function getNormalAttributeModifier($attribute)
		{
			switch($attribute)
			{
				case ($attribute < 15):
					return rand(18, 25) / 100;
				case ($attribute < 17):
					return rand(10, 18) / 100;
				case ($attribute < 20):
					return rand(4, 10) / 100;
				case ($attribute < 22):
					return rand(0, 4) / 100;
				case ($attribute < 25):
					return -(rand(0, 3) / 100);
				default:
					return -(rand(1, 4) / 100);
			}
		}
		
		protected function getHardAttributeModifier($attribute)
		{
			switch($attribute)
			{
				case ($attribute < 15):
					return rand(30, 40) / 100;
				case ($attribute < 17):
					return rand(20, 30) / 100;
				case ($attribute < 20):
					return rand(10, 20) / 100;
				case ($attribute < 22):
					return rand(0, 10) / 100;
				case ($attribute < 25):
					return 0;
				default:
					return rand(0, 5) / 100;
			}
		}
	}

?>
