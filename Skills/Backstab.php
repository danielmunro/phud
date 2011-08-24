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
	namespace Skills;
	class Backstab extends \Mechanics\Skill
	{
	
		protected $creation_points = 5;
		protected $fail_message = '';
	
		protected function __construct()
		{
			$this->alias = new \Mechanics\Alias('backstab', $this);
			$this->base_class = \Disciplines\Thief::instance();
			parent::__construct();
		}
	
		public function perform(\Mechanics\Actor $actor, $chance = 0, $args = null)
		{
			
			$roll = \Mechanics\Server::chance();
			
			$d = $actor->getDisciplineFocus()->getOtherDiscipline($actor);
			if($d instanceof \Disciplines\Thief)
				$roll -= 0.10;
			
			$roll += $this->getHardAttributeModifier($actor->getDex());
			
			if($roll < $chance)
			{
				//$actor-> do some fight stuff
			}
		}
	}
?>