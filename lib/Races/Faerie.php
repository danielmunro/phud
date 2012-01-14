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

	class Faerie extends Race
	{
		protected $alias = 'faerie';
		protected $unarmed_verb = 'slap';
		protected $size = self::SIZE_TINY;
		protected $playable = true;
		protected $proficiencies = [
			'healing' => 5,
			'alchemy' => 5,
			'elemental' => 10,
			'illusion' => 5,
			'transportation' => 5,
			'sorcery' => 10,
			'maladictions' => 5,
			'benedictions' => 5,
			'curative' => 5
		];
	
		protected function __construct()
		{
			$this->attributes = new Attributes([
				'str' => -7,
				'int' => 5,
				'wis' => 5,
				'dex' => 5,
				'con' => -7,
				'cha' => 3,
				'hit' => -1,
				'dam' => -1
			]);
			$this->addParts(['wings']);
			
			parent::__construct();
		}
		
		public function getSubscribers()
		{
			return [
				new Subscriber(
					Event::EVENT_CASTING,
					function($subscriber, $caster, $target, $spell, &$modifier, &$saves) {
						$plus_mod = rand(0.01, 0.08);
						$plus_saves = rand(1, 8);
						$modifier += $plus_mod;
						$saves += $plus_saves;
					}
				),
				new Subscriber(
					Event::EVENT_DAMAGE_MODIFIER_DEFENDING,
					function($subscriber, $victim, $attacker, &$modifier, &$dam_roll, $attacking) {
						$modifier += 0.05;
						if($attacking->getDamageType() === Damage::TYPE_POUND) {
							$modifier += 0.10;
						}
					}
				)
			];
		}

		public function getAbilities()
		{
			return [
				'fly',
				'meditation'
			];
		}
	}
?>
