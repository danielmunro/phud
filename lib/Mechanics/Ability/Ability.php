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
		\Mechanics\Server,
		\ReflectionClass,
		\Exception;

	abstract class Ability
	{
		use Alias;

		protected $proficiency = '';
		protected $required_proficiency = 0;
		protected $hard_modifier = [];
		protected $easy_modifier = [];
		protected $normal_modifier = [];
		protected $needs_target = false;
		protected $alias = '';
		
		protected function __construct()
		{
			if(empty($this->proficiency)) {
				throw new Exception(get_class($this).' is not fully configured, missing: proficiency');
			}
			if(empty($this->required_proficiency)) {
				throw new Exception(get_class($this).' is not fully configured, missing: required proficiency level');
			}
			if(empty($this->alias)) {
				throw new Exception(get_class($this).' is not fully configured, missing: alias');
			}
			self::addAlias($this->alias, $this);
		}

		public function getProficiency()
		{
			return $this->proficiency;
		}
		
		public static function runInstantiation()
		{
			$namespaces = ['Skills', 'Spells'];
			foreach($namespaces as $namespace) {
				$d = dir(dirname(__FILE__) . '/../../../deploy/init/'.$namespace);
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

		public function perform(Actor $actor, $args = [])
		{
			// check for a target if necessary
			$target = $this->determineTarget($actor, $args);
			if($this->needs_target && !$target) {
				return;
			}
			// check if actor satisfies requirements as far as mana, mv, etc
			if($this->applyCost($actor) === false) {
				return;
			}
			// do a proficiency roll to determine success or failure
			$roll = Server::chance() + ($actor->getProficiencyIn($this->proficiency) + $actor->getAttribute('saves') - (($target->getAttribute('saves') + $target->getProficiencyIn($this->proficiency))/2));
			foreach($this->hard_modifier as $m) {
				$roll += $this->getHardAttributeModifier($actor->getAttribute($m));
				$roll -= $this->getHardAttributeModifier($target->getAttribute($m));
			}
			foreach($this->normal_modifier as $m) {
				$roll += $this->getNormalAttributeModifier($actor->getAttribute($m));
				$roll -= $this->getNormalAttributeModifier($target->getAttribute($m));
			}
			foreach($this->easy_modifier as $m) {
				$roll += $this->getEasyAttributeModifier($actor->getAttribute($m));
				$roll -= $this->getEasyAttributeModifier($target->getAttribute($m));
			}
			$roll += $this->modifyRoll($actor);
			if($roll > Server::chance()) {
				return $this->success($actor, $target, $args);
			} else {
				return $this->fail($actor, $target);
			}
		}
		
		protected function determineTarget(Actor $actor, $args)
		{
		}

		protected function success(Actor $actor, Actor $target, $args = [])
		{
		}

		protected function fail(Actor $actor, Actor $target, $args = [])
		{
		}

		protected function applyCost(Actor $actor, $args = [])
		{
		}

		protected function modifyRoll(Actor $actor)
		{
		}

		protected function getEasyAttributeModifier($attribute)
		{
			switch($attribute)
			{
				case ($attribute < 15):
					return rand(12, 17);
				case ($attribute < 17):
					return rand(8, 12);
				case ($attribute < 20):
					return rand(0, 6);
				case ($attribute < 22):
					return 0;
				case ($attribute < 25):
					return -(rand(0, 5));
				default:
					return -(rand(0, 10));
			}
		}
		
		protected function getNormalAttributeModifier($attribute)
		{
			switch($attribute)
			{
				case ($attribute < 15):
					return rand(18, 25);
				case ($attribute < 17):
					return rand(10, 18);
				case ($attribute < 20):
					return rand(4, 10);
				case ($attribute < 22):
					return rand(0, 4);
				case ($attribute < 25):
					return -(rand(0, 3));
				default:
					return -(rand(1, 4));
			}
		}
		
		protected function getHardAttributeModifier($attribute)
		{
			switch($attribute)
			{
				case ($attribute < 15):
					return rand(30, 40);
				case ($attribute < 17):
					return rand(20, 30);
				case ($attribute < 20):
					return rand(10, 20);
				case ($attribute < 22):
					return rand(0, 10);
				case ($attribute < 25):
					return 0;
				default:
					return rand(0, 5);
			}
		}
	}
?>
