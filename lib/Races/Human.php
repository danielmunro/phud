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
	use \Mechanics\Alias;
	use \Mechanics\Attributes;
	class Human extends \Mechanics\Race
	{
	
		protected function __construct()
		{
			$this->alias = new Alias('human', $this);
		
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
			
			$this->movement_cost = 2;
			
			$this->decrease_thirst = 1;
			$this->decrease_nourishment = 1;
			$this->full = 40;
			
			$this->move_verb = 'walks';
			
			$this->unarmed_verb = 'punch';
			
			$this->weapons = array
			(
			);
			
			$this->playable = true;
			
			$this->available_disciplines = array(
				\Disciplines\Barbarian::instance(),
				\Disciplines\Crusader::instance(),
				\Disciplines\Rogue::instance(),
				\Disciplines\Wizard::instance()
			);
	
			$i = realpath(__DIR__.'/../../docroot').self::RESOURCE_IMAGES;
			$this->images = [
				'*' => [
					'resource' => imagecreatefrompng($i.'/human.png'),
					'abspath' => $i.'/human.png',
					'src' => 'resources/human.png'
				]
			];
		
			parent::__construct();
		}
	}
?>
