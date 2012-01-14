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
	use \Exception;

	abstract class Race
	{
		use Alias;

		const SIZE_TINY = 2;
		const SIZE_SMALL = 3;
		const SIZE_NORMAL = 4;
		const SIZE_LARGE = 5;
		const SIZE_GIGANTIC = 6;

		const FORM_HUMANOID = 'humanoid';
		const FORM_REPTILE = 'reptile';
		
		protected $attributes = null;
		protected $max_attributes = null;
		protected $affects = array();
		protected $move_verb = 'leaves';
		protected $unarmed_verb = 'punch';
		protected $size = self::SIZE_NORMAL;
		protected $playable = false;
		protected $alias = null;
		protected $proficiencies = [];
		protected $thirst = 2;
		protected $hunger = 2;
		protected $full = 4;
		protected $movement_cost = 1;
		protected $form = self::FORM_HUMANOID;
		protected $parts = ['head', 'arm', 'leg', 'heart', 'brain', 'guts', 'hand', 'foot', 'finger', 'ear', 'eye', 'long tongue', 'tentacles', 'fins', 'wings', 'tail'];
		
		protected function __construct()
		{
			if(!$this->alias) {
				throw new Exception("Need to set an alias for racial class: ".get_class($this));
			}
			self::addAlias($this->alias, $this);
			$this->setPartsFromForm();
		}

		protected function addParts($parts_add)
		{
			$this->parts = array_merge($this->parts, array_diff($this->parts, $parts_add));
		}

		protected function removeParts($parts_remove)
		{
			$this->parts = array_diff($this->parts, $parts_remove);
		}

		protected function setPartsFromForm()
		{
			if($this->form === self::FORM_HUMANOID) {
				$this->removeParts(['long tongue', 'tentacles', 'fins', 'wings', 'tail']);
			}
			else if($this->form === self::FORM_REPTILE) {
				$this->removeParts(['foot', 'finger', 'ear', 'tentacles', 'fins', 'wings']);
			}
		}

		public function getThirst()
		{
			return $this->thirst;
		}

		public function getHunger()
		{
			return $this->hunger;
		}
		
		abstract public function getSubscribers();

		abstract public function getAbilities();
		
		public function runInstantiation()
		{
			$namespace = 'Races';
			$d = dir(dirname(__FILE__) . '/../'.$namespace);
			while($race = $d->read())
				if(substr($race, -4) === ".php")
				{
					Debug::addDebugLine("init race: ".$race);
					$class = substr($race, 0, strpos($race, '.'));
					$called_class = $namespace.'\\'.$class;
					new $called_class();
				}
		}
		
		public function getProficiencies()
		{
			return $this->proficiencies;
		}

		public static function getParts(Actor $actor)
		{
			// @todo finish parts... this can wait for other more important things
			$parts = array
			(
				self::PART_HEAD => "'s severed head rolls to the floor",
				self::PART_ARM => "'s arm is sliced from ",
				self::PART_LEG => 'leg',
				self::PART_HEART => 'heart'
			);
		}
		
		public function getAttributes()
		{
			return $this->attributes;
		}
		
		public function getMaxAttributes()
		{
			return $this->max_attributes;
		}
		
		public function getSize()
		{
			return $this->size;
		}

		public function getMovementCost()
		{
			return $this->movement_cost;
		}

		public function getUnarmedVerb()
		{
			return $this->unarmed_verb;
		}

		public function getMoveVerb()
		{
			return $this->move_verb;
		}

		public function getFull()
		{
			return $this->full;
		}

		public function isPlayable()
		{
			return $this->playable;
		}
		
		public function getAlias()
		{
			return $this->alias;
		}
		
		public function __toString()
		{
			if($this->alias)
				return $this->alias->getAliasName();
			return '';
		}

		public function __sleep()
		{
			return [
				'alias'
			];
		}
	}
?>
