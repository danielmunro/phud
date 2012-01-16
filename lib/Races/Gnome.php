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
		\Mechanics\Server,
		\Mechanics\Attributes;

	class Gnome extends Race
	{
		protected $alias = 'gnome';
		protected $playable = true;
		protected $proficiencies = [
			'stealth' => 15,
			'one handed weapons' => 5,
			'leather armor' => 5,
			'alchemy' => 10,
			'illusion' => 10,
			'evasive' => 5,
			'speech' => 10
		];
	
		protected function __construct()
		{
			$this->attributes = new Attributes([
				'str' => -2,
				'int' => 0,
				'wis' => 0,
				'dex' => 5,
				'con' => -3,
				'cha' => 1,
				'saves' => 2,
				'movement' => 50
			]);

			parent::__construct();
		}
		
		public function getSubscribers()
		{
			return [
				new Subscriber(
					Event::EVENT_BASHED,
					function($subscriber, $target, $roll) {
						$roll -= 10;
					}
				)
			];
		}

		public function getAbilities()
		{
			return [
				'sneak',
				'haggle'
			];
		}
	}
?>
