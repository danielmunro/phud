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
		\Mechanics\Effect,
		\Mechanics\Damage,
		\Mechanics\Attributes,
		\Mechanics\Server,
		\Mechanics\Event\Subscriber,
		\Mechanics\Event\Event;

	class Ogre extends Race
	{
		protected $alias = 'ogre';
		protected $creation_points = 4;
		protected $movement_cost = 2;
		protected $full = 2;
		protected $hunger = 4;
		protected $thirst = 2;
		protected $unarmed_verb = 'pummel';
		protected $size = self::SIZE_LARGE;
		protected $playable = true;
		protected $proficiencies = [
			'one handed weapons' => 10,
			'two handed weapons' => 10,
			'chain armor' => 5,
			'plate armor' => 5,
			'melee' => 10,
			'alchemy' => 5,
			'curative' => 5,
			'evasive' => 5
		];
	
		protected function __construct()
		{
			self::addAlias('ogre', $this);
		
			$this->attributes = new Attributes([
				'str' => 5,
				'int' => -5,
				'wis' => -2,
				'dex' => -1,
				'con' => 3,
				'cha' => -3,
				'dam' => 2,
				'saves' => 10
			]);

			parent::__construct();
		}

		public function getSubscribers()
		{
			return [
				new Subscriber(
					Event::EVENT_MELEE_ATTACK,
					function($subscriber, $attacker) {
						if(Server::chance() < 5) {
							$attacker->attack('Ogr');
						}
					}
				),
				new Subscriber(
					Event::EVENT_DAMAGE_MODIFIER_DEFENDING,
					function($subscriber, $attacker, $victim, &$modifier, &$dam_roll, $attacking) {
						if($attacking && method_exists($attacking, 'getDamageType')) {
							$d = $attacking->getDamageType();
							if($d === Damage::TYPE_FIRE || $d === Damage::TYPE_FROST) {
								$modifier -= 0.15;
							}
							if($d === Damage::TYPE_MAGIC || $d === Damage::TYPE_MENTAL) {
								$modifier += 0.10;
							}
						}
					}
				)
			];
		}

		public function getAbilities()
		{
			return [
				'enhanced damage'
			];
		}
	}
?>
