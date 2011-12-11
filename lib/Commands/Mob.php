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
	use \Mechanics\Server,
		\Mechanics\Alias,
		\Mechanics\Race,
		\Mechanics\Command\DM,
		\Living\Mob as lMob,
		\Living\User as lUser;

	class Mob extends DM
	{
		protected function __construct()
		{
			new Alias('mob', $this);
		}
	
		public function perform(lUser $user, $args = array())
		{
			if(sizeof($args) < 3)
				return Server::out($user, "What were you trying to do?");
		
			$command = $this->getCommand($args[2]);
			$mob = $user->getRoom()->getUserByInput($args[1]);
			$value = implode(' ', array_slice($args, 3));
			
			if($mob instanceof lMob && $command)
			{
				$fn = 'do'.ucfirst($command);
				return $this->$fn($user, $mob, $value, $args);
			}
			if(!($mob instanceof lMob))
				return Server::out($user, "They aren't here.");
			Server::out($user, "What do you want to do to ".$mob->getAlias()."?");
		}
		
		private function doRace(User $user, lMob $mob, $race, $args)
		{
			$race = Alias::lookup($arg_race);
			if(!($race instanceof Race))
				return Server::out($user, "That is not a valid race.");
			
			$mob->setRace($race);
			$mob->save();
			$mob->getRoom()->announce($mob, $mob->getAlias(true)." shapeshifts into a ".$race->getAlias().".");
		}
		
		private function doLong(User $user, lMob $mob, $long, $args)
		{
			$mob->setLong($arg_long);
			$mob->save();
			
			Server::out($user, $mob->getAlias(true)."'s description now reads: ".$mob->getLong());
		}
		
		private function doLevel(User $user, lMob $mob, $level, $args)
		{
			if(!is_numeric($level))
				return Server::out($user, "Number of levels granted must be a number.");
			
			$mob->setLevel($level);
			$mob->save();
			
			return Server::out($user, "You grant ".$mob->getAlias()." ".$level." level".($level==1?'':'s'));
		}
		
		private function doInformation(User $user, lMob $mob, $inf, $args)
		{
			Server::out($user,
					"info page on mob:\n".
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
		
		private function doGold(User $user, lMob $mob, $value, $args)
		{
			$this->doWorth($user, $mob, $value, $args, 'gold');
		}
		
		private function doSilver(User $user, lMob $mob, $value, $args)
		{
			$this->doWorth($user, $mob, $value, $args, 'silver');
		}
		
		private function doCopper(User $user, lMob $mob, $value, $args)
		{
			$this->doWorth($user, $mob, $value, $args, 'copper');
		}
		
		private function doWorth(User $user, lMob $mob, $amount, $args, $type)
		{
			if(!is_numeric($amount) || $amount < 0 || $amount > 99999)
				return Server::out($user, "Invalid amount of ".$type." to give ".$mob->getAlias().".");
			
			$fn = 'set'.ucfirst($type).'Repop';
			$mob->$fn($amount);
			$fn = 'set'.ucfirst($type);
			$mob->$fn($amount);
			Server::out($user, "You set ".$mob->getAlias()."'s ".$type." amount to ".$amount.".");
		}
		
		private function doRespawn(User $user, lMob $mob, $ticks, $args)
		{
			if(!is_numeric($ticks))
				return Server::out($user, "What respawn time?");
			
			$mob->setDefaultRespawnTicks($ticks);
			Server::out($user, "You set ".$mob->getAlias()."'s respawn to ".$ticks." ticks.");
		}
		
		private function doSex(User $user, lMob $mob, $sex, $args)
		{
			if($mob->setSex($sex))
				return Server::out($user, $mob->getAlias(true)." is now a ".strtoupper($mob->getDisplaySex()).".");
		}
		
		private function doAutoflee(User $user, lMob $mob, $auto_flee, $args)
		{
			$mob->setAutoFlee($auto_flee);
			Server::out($user, $mob->getAlias(true)."'s auto flee is set to ".$auto_flee." hp.");
		}
		
		private function doMovement(User $user, lMob $mob, $movement, $args)
		{
			$mob->setMovementTicks($movement);
			Server::out($user, $mob->getAlias()."'s movement speed set to ".$movement." ticks.");
		}
		
		private function doArea(User $user, lMob $mob, $area, $args)
		{
			$mob->setArea($area);
			Server::out($user, $mob->getAlias(true)."'s area is now set to ".$area.".");
		}
		
		private function doHp(User $user, lMob $mob, $hp, $args)
		{
			$mob->setHp($hp);
			$mob->setMaxHp($hp);
			Server::out($user, $mob->getAlias(true)."'s hp is now set to ".$hp.".");
		}
		
		private function doMana(User $user, lMob $mob, $mana, $args)
		{
			$mob->setMana($mana);
			$mob->setMaxMana($mana);
			Server::out($user, $mob->getAlias(true)."'s mana is now set to ".$mana.".");
		}
		
		private function doMv(User $user, lMob $mob, $movement, $args)
		{
			$mob->setMovement($movement);
			$mob->setMaxMovement($movement);
			Server::out($user, $mob->getAlias(true)."'s movement points are now set to ".$movement.".");
		}
		
		private function doAlias(User $user, lMob $mob, $alias, $args)
		{
			$old_alias = $mob->getAlias(true);
			$mob->setAlias($alias);
			Server::out($user, $old_alias."'s alias has changed to: ".$mob->getAlias().".");
		}
		
		private function doNouns(User $user, lMob $mob, $nouns, $args)
		{
			$mob->setNouns($nouns);
			Server::out($user, $mob->getAlias(true)."'s nouns have changed to: ".$mob->getNouns().".");
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
