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
		\Mechanics\Event\Subscriber,
		\Mechanics\Event\Event,
		\Mechanics\Attributes;

	class Human extends Race
	{
		protected $alias = 'human';
		protected $creation_points = 10;
		protected $playable = true;
		protected $proficiencies = [
			'alchemy' => 10,
			'one handed weapons' => 5,
			'melee' => 5,
			'evasive' => 5,
			'speech' => 5,
			'leather armor' => 5,
			'elemental' => 5,
			'benedictions' => 5,
			'healing' => 5,
			'beguiling' => 5
		];

		protected function __construct()
		{
			self::addAlias('human', $this);
		
			$this->attributes = new Attributes();
			$this->max_attributes = new Attributes();
			
			$this->attributes->setStr(13);
			$this->attributes->setInt(13);
			$this->attributes->setWis(13);
			$this->attributes->setDex(13);
			$this->attributes->setCon(13);
			$this->attributes->setCha(13);
			$this->attributes->setAcBash(100);
			$this->attributes->setAcSlash(100);
			$this->attributes->setAcPierce(100);
			$this->attributes->setAcMagic(100);
			$this->attributes->setHit(1);
			$this->attributes->setDam(2);
		
			$this->max_attributes->setStr(18);
			$this->max_attributes->setInt(18);
			$this->max_attributes->setWis(18);
			$this->max_attributes->setDex(18);
			$this->max_attributes->setCon(18);
			$this->max_attributes->setCha(18);
			
			$this->subscribers = [
				new Subscriber(
					Event::EVENT_MOVED,
					function($subscriber, $actor, $increase_movement) {
						$increase_movement(1);
					}
				)
			];

			parent::__construct();
		}

		public function getSubscribers()
		{
			return $this->subscribers;
		}
	}
?>
