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
	namespace Commands;
	use \Mechanics\Alias,
		\Mechanics\Server,
		\Mechanics\Actor,
		\Mechanics\Command\Command,
		\Living\Shopkeeper as lShopkeeper,
		\Living\User as lUser;
	
	class Give extends Command
	{
		protected $dispositions = [
			Actor::DISPOSITION_STANDING,
			Actor::DISPOSITION_SITTING
		];
	
		protected function __construct()
		{
			self::addAlias('give', $this);
		}
	
		public function perform(Actor $actor, $args = [])
		{
			if(sizeof($args) === 3) {
				$arg_target = $args[2];
				$arg_item = $args[1];
				$amount = 1;
			} else if(sizeof($args) === 4) {
				$arg_target = $args[3];
				$arg_item = $args[2];
				$amount = abs($args[1]);
			} else {
				return Server::out($actor, "What did you want to give?");
			}

			// Item quantity not figured out yet
			$target = $actor->getRoom()->getActorByInput($arg_target);
			$item = $actor->getItemByInput($arg_item, $amount);

			if(!$target) {
				return Server::out($actor, "They are not there.");
			}
			
			if($item) {
				$actor->removeItem($item);
				$target->addItem($item);
				$actor->getRoom()->announce2([
					['actor' => $actor,
					'message' => "You give ".$item." to ".$target."."],
					['actor' => $target,
					'message' => ucfirst($actor)." gives ".$item." to you."],
					['actor' => '*',
					'message' => ucfirst($actor)." gives ".$item." to ".$target."."]
				]);
				return;
			}
			
			$currency = '';
			if(strpos('gold', $args[2]) === 0) {
				$currency = 'gold';
			}
			else if(strpos('silver', $args[2]) === 0) {
				$currency = 'silver';
			}
			else if(strpos('copper', $args[2]) === 0) {
				$currency = 'copper';
			}

			if($currency) {
				if($amount < $actor->getCurrency($currency)) {
					return Server::out($actor, "You do not have enough ".$currency.".");
				}
				$actor->modifyCurrency(-($currency), $amount);
				$target->modifyCurrency($currency, $amount);
				$actor->getRoom()->announce2([
					['actor' => $actor,
					'message' => "You give ".$amount." ".$currency." to ".$target."."],
					['actor' => $target,
					'message' => ucfirst($actor)." gives ".$amount." ".$currency." to you."],
					['actor' => '*',
					'message' => ucfirst($actor)." gives ".$amount." ".$currency." to ".$target."."]
				]);
				return;
			}

			return Server::out($actor, "What are you trying to give?");
		}
	}
?>
