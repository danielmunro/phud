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

	class Volare extends Race
	{
		protected $alias = 'volare';
		protected $playable = true;
		protected $proficiencies = [
			'healing' => 10,
			'benedictions' => 10,
			'curative' => 10,
			'one handed weapons' => 5,
			'leather armor' => 5,
			'speech' => 5
		];
	
		protected function __construct()
		{
			$this->attributes = new Attributes([
				'str' => -5,
				'int' => 4,
				'wis' => 4,
				'dex' => 1,
				'con' => -4,
				'cha' => 2
			]);

			parent::__construct();
		}

		protected function setPartsFromForm()
		{
			parent::setPartsFromForm();
			$this->addParts(['wings']);
		}
		
		public function getSubscribers()
		{
			return [
				new Subscriber(
					Event::EVENT_HEALING,
					function($subscriber, $caster, $target, $spell, $modifier, $saves) {
						$modifier += 0.10;
					}
				)
			];
		}

		public function getAbilities()
		{
			return [
				'cure light',
				'armor',
				'meditation'
			];
		}
	}
?>
