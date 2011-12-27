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
	namespace Races;
	use \Mechanics\Alias,
		\Mechanics\Race,
		\Mechanics\Attributes;

	class Undead extends Race
	{
		protected $alias = 'undead';
		protected $creation_points = 30;
		protected $full = 5;
		protected $hunger = 3;
		protected $movement_cost = 2;
		protected $unarmed_verb = 'swipe';
		protected $move_verb = 'limps';
		protected $playable = true;
		protected $proficiencies = [
			'one handed weapons' => 5,
			'two handed weapons' => 5,
			'melee' => 10,
			'sorcery' => 10,
			'maladictions' => 10,
			'transportation' => 5,
			'illusion' => 5,
			'elemental' => 5
		];

		protected function __construct()
		{
			self::addAlias('undead', $this);
		
			$this->attributes = new Attributes();
			$this->max_attributes = new Attributes();
			
			$this->attributes->setStr(15);
			$this->attributes->setInt(15);
			$this->attributes->setWis(13);
			$this->attributes->setDex(12);
			$this->attributes->setCon(12);
			$this->attributes->setCha(6);
			$this->attributes->setAcBash(100);
			$this->attributes->setAcSlash(100);
			$this->attributes->setAcPierce(100);
			$this->attributes->setAcMagic(100);
			$this->attributes->setHit(1);
			$this->attributes->setDam(1);
			
			$this->max_attributes->setStr(21);
			$this->max_attributes->setInt(21);
			$this->max_attributes->setWis(17);
			$this->max_attributes->setDex(16);
			$this->max_attributes->setCon(17);
			$this->max_attributes->setCha(13);
			
			parent::__construct();
		}

		public function getSubscribers()
		{
			return [
			];
		}
	}
?>
