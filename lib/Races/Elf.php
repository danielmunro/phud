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
		\Mechanics\Item,
		\Mechanics\Attributes;

	class Elf extends Race
	{
		protected $alias = 'elf';
		protected $creation_points = 14;
		protected $playable = true;
		protected $proficiencies = [
			'stealth' => 10,
			'one handed weapons' => 5,
			'leather armor' => 5,
			'archery' => 10,
			'elemental' => 5,
			'illusion' => 5,
			'evasive' => 5,
			'speech' => 5,
			'beguiling' => 5
		];
	
		protected function __construct()
		{
			self::addAlias('elf', $this);
		
			$this->attributes = new Attributes();
			$this->max_attributes = new Attributes();
			
			$this->attributes->setStr(10);
			$this->attributes->setInt(15);
			$this->attributes->setWis(14);
			$this->attributes->setDex(14);
			$this->attributes->setCon(12);
			$this->attributes->setCha(13);
			$this->attributes->setAcBash(100);
			$this->attributes->setAcSlash(100);
			$this->attributes->setAcPierce(100);
			$this->attributes->setAcMagic(100);
			$this->attributes->setHit(1);
			$this->attributes->setDam(1);
			
			$this->max_attributes->setStr(17);
			$this->max_attributes->setInt(21);
			$this->max_attributes->setWis(19);
			$this->max_attributes->setDex(21);
			$this->max_attributes->setCon(17);
			$this->max_attributes->setCha(21);
			
			parent::__construct();
		}
		
		public function getSubscribers()
		{
			return [
				new Subscriber(
					Event::EVENT_DAMAGE_MODIFIER,
					function($subscriber, $broadcaster, $victim, &$dam_roll, $attacking_weapon) {
						if($attacking_weapon->getMaterial() === Item::MATERIAL_IRON) {
							$dam_roll *= 1.15;
						}
					}
				),
				new Subscriber(
					Event::EVENT_CAST_TARGET,
					function($subscriber, $broadcaster, $spell, &$chance) {
						if($spell['lookup']->getProficiency() === 'beguiling') {
							$chance -= 0.25;
							if($spell['alias'] === 'sleep') {
								$chance -= 0.25;
							}
						}
					}
				)
			];
		}
	}
?>
