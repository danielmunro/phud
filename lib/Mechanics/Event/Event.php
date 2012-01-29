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
	
	namespace Mechanics\Event;

	class Event
	{
		const EVENT_MOVED = 'moved';
		const EVENT_INPUT = 'input';
		const EVENT_PULSE = 'pulse';
		const EVENT_TICK = 'tick';
		const EVENT_CONNECTED = 'connected';
		const EVENT_GAME_CYCLE = 'cycle';
		const EVENT_ATTACKED = 'attacked';
		const EVENT_MELEE_ATTACK = 'melee attack';
		const EVENT_MELEE_ATTACKED = 'melee attacked';
		const EVENT_DAMAGE_MODIFIER_ATTACKING = 'single round attack modifier';
		const EVENT_DAMAGE_MODIFIER_DEFENDING = 'single round defense modifier';
		const EVENT_CASTING = 'casting';
		const EVENT_CASTED_AT = 'casted at';
		const EVENT_BUY = 'buy';
		const EVENT_BASHED = 'bashed';
		const EVENT_APPLY_AFFECT = 'apply affect';
	}
?>
