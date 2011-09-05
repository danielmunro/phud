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
	use \Mechanics\Server;
	use \Mechanics\Alias;
	use \Mechanics\Actor;
	use \Mechanics\Race;
	class Shopkeeper extends \Mechanics\Command implements \Mechanics\Command_DM
	{
	
		protected function __construct()
		{
			new Alias('shopkeeper', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
			if(sizeof($args) <= 1)
				return Server::out($actor, "What were you trying to do?");
		
			$command = $this->getCommand($args[1]);
			if($command)
			{
				$fn = 'do'.ucfirst($command);
				$this->$fn($actor, $args);
			}
		}
		
		private function doRace(Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$arg_race = implode(' ', array_slice($args, sizeof($args) - 1));
			$arg_noun = implode(' ', array_slice($args, 2, 1));
			
			$race = Alias::lookup($arg_race);
			$shopkeeper = $actor->getRoom()->getActorByInput($arg_noun);
			
			if(!($race instanceof Race))
				return Server::out($actor, "That is not a valid race.");
			
			if(!($shopkeeper instanceof \Living\Shopkeeper))
				return Server::out($actor, "Who?");
			
			$shopkeeper->setRace($race);
			$shopkeeper->save();
			$shopkeeper->getRoom()->announce($shopkeeper, $shopkeeper->getAlias(true)." spontaneously shapeshifts into a ".$race->getAlias().".");
		}
		
		private function doLong(\Mechanics\Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$arg_noun = implode(' ', array_slice($args, 2, 1));
			$arg_long = implode(' ', array_slice($args, 3));
			
			$shopkeeper = $actor->getRoom()->getActorByInput($arg_noun);
			
			if(!($shopkeeper instanceof \Living\Shopkeeper))
				return Server::out($actor, "Who is that?");
			
			$shopkeeper->setLong($arg_long);
			$shopkeeper->save();
			
			Server::out($actor, $shopkeeper->getAlias(true)."'s description now reads: ".$shopkeeper->getLong());
		}
		
		private function doLevel(Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 3))
				return;
			
			$shopkeeper = $actor->getRoom()->getActorByInput($args[2]);
			if(!($shopkeeper instanceof \Living\Shopkeeper))
				return Server::out($actor, "What shopkeeper do you want to grant a level to?");
			
			$num_levels = 1;
			if(sizeof($args) == 4)
				$num_levels = $args[3];
			
			if(!is_numeric($num_levels))
				return Server::out($actor, "Number of levels granted must be a number.");
			
			$shopkeeper->setLevel($num_levels);
			$shopkeeper->save();
			
			return Server::out($actor, "You grant ".$shopkeeper->getAlias()." ".$num_levels." level".($num_levels==1?'':'s'));
		}
		
		private function doInformation(Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 3))
				return;
			
			array_shift($args);
			$shopkeeper = $actor->getRoom()->getActorByInput($args);
			
			if(!($shopkeeper instanceof \Living\Shopkeeper))
				return Server::out($actor, "That's not a shopkeeper.");
			
			Server::out($actor,
					"info page on shopkeeper #".$shopkeeper->getId().":\n".
					"alias:                    ".$shopkeeper->getAlias()."\n".
					"race:                     ".$shopkeeper->getRace()."\n".
					"level:                    ".$shopkeeper->getLevel()."\n".
					"nouns:                    ".$shopkeeper->getNouns()."\n".
					"max worth:                ".$shopkeeper->getGold().'g '.$shopkeeper->getSilver().'s '.$shopkeeper->getCopper()."c\n".
					"movement ticks:           ".$shopkeeper->getMovementTicks()."\n".
					"unique:                   ".($shopkeeper->isUnique()?'yes':'no')."\n".
					"sex:                      ".($shopkeeper->getSex()=='m'?'male':'female')."\n".
					"start room:               ".$shopkeeper->getStartRoom()->getTitle()." (#".$shopkeeper->getStartRoom()->getId().")\n".
					"area:                     ".$shopkeeper->getArea()."\n".
					"long:\n".
					($shopkeeper->getLong() ? $shopkeeper->getLong() : "Nothing."));
		}
		
		private function doGold(Actor $actor, $args)
		{
			$this->doWorth($actor, $args, 'gold');
		}
		
		private function doSilver(Actor $actor, $args)
		{
			$this->doWorth($actor, $args, 'silver');
		}
		
		private function doCopper(Actor $actor, $args)
		{
			$this->doWorth($actor, $args, 'copper');
		}
		
		private function doWorth(Actor $actor, $args, $type)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$shopkeeper = $actor->getRoom()->getActorByInput($args[2]);
			if(!($shopkeeper instanceof \Living\Shopkeeper))
				return Server::out($actor, "Shopkeeper not here.");
			
			$amount = $args[3];
			if(!is_numeric($amount) || $amount < 0 || $amount > 99999)
				return Server::out($actor, "Invalid amount of ".$type." to give ".$shopkeeper->getAlias().".");
			
			$fn = 'set'.ucfirst($type).'Repop';
			$shopkeeper->$fn($amount);
			$fn = 'set'.ucfirst($type);
			$shopkeeper->$fn($amount);
			Server::out($actor, "You set ".$shopkeeper->getAlias()."'s ".$type." amount to ".$amount.".");
		}
		
		private function doSex(Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$shopkeeper = $actor->getRoom()->getActorByInput($args[2]);
			if(!($shopkeeper instanceof \Living\Shopkeeper))
				return Server::out($actor, "That's not a shopkeeper.");
			
			$sex = array_pop($args);
			if($shopkeeper->setSex($sex))
				return Server::out($actor, $shopkeeper->getAlias(true)." is now a ".strtoupper($shopkeeper->getDisplaySex()).".");
		}
		
		private function doMovement(Actor $actor, $args)
		{
			$movement = array_pop($args);
			if(!is_numeric($movement))
				return Server::out($actor, "What movement speed?");
			
			$shopkeeper = $actor->getRoom()->getActorByInput($args[2]);
			if(!($shopkeeper instanceof \Living\Shopkeeper))
				return Server::out($actor, "That's not a shopkeeper.");
			
			$shopkeeper->setMovementTicks($movement);
			Server::out($actor, $shopkeeper->getAlias()."'s movement speed set to ".$movement." ticks.");
		}
		
		private function doArea(Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$shopkeeper = $actor->getRoom()->getActorByInput($args[2]);
			if(!($shopkeeper instanceof \Living\Shopkeeper))
				return Server::out($actor, "That's not a shopkeeper.");
			
			$area = implode(' ', array_slice($args, 3));
			
			$shopkeeper->setArea($area);
			Server::out($actor, $shopkeeper->getAlias(true)."'s area is now set to ".$area.".");
		}
		
		private function doAlias(Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$shopkeeper = $actor->getRoom()->getActorByInput($args[2]);
			if(!($shopkeeper instanceof \Living\Shopkeeper))
				return Server::out($actor, "That's not a shopkeeper.");
			$old_alias = $shopkeeper->getAlias(true);
			$alias = implode(' ', array_slice($args, 3));
			
			$shopkeeper->setAlias($alias);
			Server::out($actor, $old_alias." has been renamed to ".$shopkeeper->getAlias().".");
		}
		
		private function doDelete(Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 3))
				return;
			
			$shopkeeper = $actor->getRoom()->getActorByInput($args[2]);
			if(!($shopkeeper instanceof \Living\Shopkeeper))
				return Server::out($actor, "That's not a shopkeeper.");
			
			$shopkeeper->delete();
			$actor->getRoom()->announce($shopkeeper, $shopkeeper->getAlias(true)." poofs out of existence.");
		}
		
		private function getCommand($arg)
		{
			$commands = array('race', 'delete', 'alias', 'level', 'information', 'long', 'gold', 'silver', 'copper', 'movement', 'sex', 'area');
			
			$command = array_filter($commands, function($c) use ($arg) 
				{
					return strpos($c, $arg) === 0;
				});
			
			if(sizeof($command))
				return str_replace(' ', '', array_shift($command));
			
			return false;
		}
	}
?>
