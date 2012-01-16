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

	class Giant extends Race
	{
		protected $alias = 'giant';
		protected $movement_cost = 2;
		protected $full = 2;
		protected $hunger = 4;
		protected $thirst = 3;
		protected $unarmed_verb = 'pummel';
		protected $size = self::SIZE_GIGANTIC;
		protected $playable = true;
		protected $proficiencies = [
			'one handed weapons' => 10,
			'two handed weapons' => 10,
			'leather armor' => 5,
			'melee' => 10,
			'alchemy' => 10,
			'elemental' => 10
		];
	
		protected function __construct()
		{
			$this->attributes = new Attributes([
				'str' => 7,
				'int' => -5,
				'wis' => -4,
				'dex' => 0,
				'con' => 4,
				'cha' => -3,
				'dam' => 2
			]);

			parent::__construct();
		}

		public function getSubscribers()
		{
			return [
				new Subscriber(
					Event::EVENT_MOVED,
					function($subscriber, $actor, $movement_cost, $room) {
						$t = $room->getTerrainType();
						if($t === Room::TERRAIN_HILLS ||
							$t === Room::TERRAIN_MOUNTAINS ||
							$t === Room::TERRAIN_GRASSLANDS) {
							$movement_cost /= 2;
						}
					}
				),
				new Subscriber(
					Event::EVENT_DAMAGE_MODIFIER_DEFENDING,
					function($subscriber, $victim, $attacker, $modifier, $dam_roll) {
						$modifier -= 0.05;
					}
				),
				new Subscriber(
					Event::EVENT_CASTED_AT,
					function($subscriber, $target, $caster, $spell, $modifier, $saves) {
						$modifier += 0.08;
					}
				)
			];
		}

		public function getAbilities()
		{
			return [
				'bash',
				'meditation'
			];
		}
	}
?>
