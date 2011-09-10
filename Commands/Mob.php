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
	use \Living\Mob as lMob;
	class Mob extends \Mechanics\Command implements \Mechanics\Command_DM
	{
	
		protected function __construct()
		{
			new Alias('mob', $this);
		}
	
		public function perform(Actor $actor, $args = array())
		{
			if(sizeof($args) < 3)
				return Server::out($actor, "What were you trying to do?");
		
			$command = $this->getCommand($args[2]);
			$mob = $actor->getRoom()->getActorByInput($args[1]);
			$value = implode(' ', array_slice($args, 3));
			
			if($mob instanceof lMob && $command)
			{
				$fn = 'do'.ucfirst($command);
				return $this->$fn($actor, $mob, $value, $args);
			}
			if(!($mob instanceof lMob))
				return Server::out($actor, "They aren't here.");
			Server::out($actor, "What do you want to do to ".$mob->getAlias()."?");
		}
		
		private function doRace(Actor $actor, lMob $mob, $race, $args)
		{
			$race = Alias::lookup($arg_race);
			if(!($race instanceof Race))
				return Server::out($actor, "That is not a valid race.");
			
			$mob->setRace($race);
			$mob->save();
			$mob->getRoom()->announce($mob, $mob->getAlias(true)." shapeshifts into a ".$race->getAlias().".");
		}
		
		private function doLong(Actor $actor, lMob $mob, $long, $args)
		{
			$mob->setLong($arg_long);
			$mob->save();
			
			Server::out($actor, $mob->getAlias(true)."'s description now reads: ".$mob->getLong());
		}
		
		private function doLevel(Actor $actor, lMob $mob, $level, $args)
		{
			if(!is_numeric($level))
				return Server::out($actor, "Number of levels granted must be a number.");
			
			$mob->setLevel($level);
			$mob->save();
			
			return Server::out($actor, "You grant ".$mob->getAlias()." ".$level." level".($level==1?'':'s'));
		}
		
		private function doInformation(Actor $actor, lMob $mob, $inf, $args)
		{
			Server::out($actor,
					"info page on mob #".$mob->getId().":\n".
					"alias:                    ".$mob->getAlias()."\n".
					"race:                     ".$mob->getRace()."\n".
					"level:                    ".$mob->getLevel()."\n".
					"nouns:                    ".$mob->getNouns()."\n".
					"stats:                    ".$mob->getHp().'/'.$mob->getMaxHp().'hp '.$mob->getMana().'/'.$mob->getMaxMana().'m '.$mob->getMovement().'/'.$mob->getMaxMovement()."v\n".
					"max worth:                ".$mob->getGold().'g '.$mob->getSilver().'s '.$mob->getCopper()."c\n".
					"movement ticks:           ".$mob->getMovementTicks()."\n".
					"auto flee:                ".$mob->getAutoFlee()."\n".
					"unique:                   ".($mob->isUnique()?'yes':'no')."\n".
					"respawn time:             ".$mob->getDefaultRespawnTicks()."\n".
					"sex:                      ".($mob->getSex()=='m'?'male':'female')."\n".
					"start room:               ".$mob->getStartRoom()->getTitle()." (#".$mob->getStartRoom()->getId().")\n".
					"area:                     ".$mob->getArea()."\n".
					"long:\n".
					($mob->getLong() ? $mob->getLong() : "Nothing."));
		}
		
		private function doGold(Actor $actor, lMob $mob, $value, $args)
		{
			$this->doWorth($actor, $mob, $value, $args, 'gold');
		}
		
		private function doSilver(Actor $actor, lMob $mob, $value, $args)
		{
			$this->doWorth($actor, $mob, $value, $args, 'silver');
		}
		
		private function doCopper(Actor $actor, lMob $mob, $value, $args)
		{
			$this->doWorth($actor, $mob, $value, $args, 'copper');
		}
		
		private function doWorth(Actor $actor, lMob $mob, $amount, $args, $type)
		{
			if(!is_numeric($amount) || $amount < 0 || $amount > 99999)
				return Server::out($actor, "Invalid amount of ".$type." to give ".$mob->getAlias().".");
			
			$fn = 'set'.ucfirst($type).'Repop';
			$mob->$fn($amount);
			$fn = 'set'.ucfirst($type);
			$mob->$fn($amount);
			Server::out($actor, "You set ".$mob->getAlias()."'s ".$type." amount to ".$amount.".");
		}
		
		private function doRespawn(Actor $actor, lMob $mob, $ticks, $args)
		{
			if(!is_numeric($ticks))
				return Server::out($actor, "What respawn time?");
			
			$mob->setDefaultRespawnTicks($ticks);
			Server::out($actor, "You set ".$mob->getAlias()."'s respawn to ".$ticks." ticks.");
		}
		
		private function doSex(Actor $actor, lMob $mob, $sex, $args)
		{
			if($mob->setSex($sex))
				return Server::out($actor, $mob->getAlias(true)." is now a ".strtoupper($mob->getDisplaySex()).".");
		}
		
		private function doAutoflee(Actor $actor, lMob $mob, $auto_flee, $args)
		{
			$mob->setAutoFlee($auto_flee);
			Server::out($actor, $mob->getAlias(true)."'s auto flee is set to ".$auto_flee." hp.");
		}
		
		private function doMovement(Actor $actor, lMob $mob, $movement, $args)
		{
			$mob->setMovementTicks($movement);
			Server::out($actor, $mob->getAlias()."'s movement speed set to ".$movement." ticks.");
		}
		
		private function doArea(Actor $actor, lMob $mob, $area, $args)
		{
			$mob->setArea($area);
			Server::out($actor, $mob->getAlias(true)."'s area is now set to ".$area.".");
		}
		
		private function doHp(Actor $actor, lMob $mob, $hp, $args)
		{
			$mob->setHp($hp);
			$mob->setMaxHp($hp);
			Server::out($actor, $mob->getAlias(true)."'s hp is now set to ".$hp.".");
		}
		
		private function doMana(Actor $actor, lMob $mob, $mana, $args)
		{
			$mob->setMana($mana);
			$mob->setMaxMana($mana);
			Server::out($actor, $mob->getAlias(true)."'s mana is now set to ".$mana.".");
		}
		
		private function doMv(Actor $actor, lMob $mob, $movement, $args)
		{
			$mob->setMovement($movement);
			$mob->setMaxMovement($movement);
			Server::out($actor, $mob->getAlias(true)."'s movement points are now set to ".$movement.".");
		}
		
		private function doAlias(Actor $actor, lMob $mob, $alias, $args)
		{
			$old_alias = $mob->getAlias(true);
			$mob->setAlias($alias);
			Server::out($actor, $old_alias."'s alias has changed to: ".$mob->getAlias().".");
		}
		
		private function doNouns(Actor $actor, lMob $mob, $nouns, $args)
		{
			$mob->setNouns($nouns);
			Server::out($actor, $mob->getAlias(true)."'s nouns have changed to: ".$mob->getNouns().".");
		}
		
		private function getCommand($arg)
		{
			$commands = array('alias', 'nouns', 'race', 'level', 'hp', 'mana', 'mv', 'information', 'long', 'gold', 'silver', 'copper', 'respawn', 'movement', 'autoflee', 'sex', 'area');
			
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
