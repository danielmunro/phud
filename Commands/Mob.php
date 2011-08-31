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
	class Mob extends \Mechanics\Command implements \Mechanics\Command_DM
	{
	
		protected function __construct()
		{
			new \Mechanics\Alias('mob', $this);
		}
	
		public function perform(\Mechanics\Actor $actor, $args = array())
		{
		
			if(sizeof($args) <= 1)
				return \Mechanics\Server::out($actor, "What were you trying to do?");
		
			$command = $this->getCommand($args[1]);
			if($command)
			{
				$fn = 'do'.ucfirst($command);
				$this->$fn($actor, $args);
			}
		}
		
		private function doRace(\Mechanics\Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$arg_race = implode(' ', array_slice($args, sizeof($args) - 1));
			$arg_noun = implode(' ', array_slice($args, 2, 1));
			
			$race = \Mechanics\Alias::lookup($arg_race);
			$mob = $actor->getRoom()->getActorByInput($arg_noun);
			
			if(!($race instanceof \Mechanics\Race))
				return \Mechanics\Server::out($actor, "That is not a valid race.");
			
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "Who?");
			
			$mob->setRace($race);
			$mob->save();
			$mob->getRoom()->announce($mob, $mob->getAlias(true)." spontaneously shapeshifts into a ".$race->getAlias().".");
		}
		
		private function doLong(\Mechanics\Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$arg_noun = implode(' ', array_slice($args, 2, 1));
			$arg_long = implode(' ', array_slice($args, 3));
			
			$mob = $actor->getRoom()->getActorByInput($arg_noun);
			
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "Who is that?");
			
			$mob->setLong($arg_long);
			$mob->save();
			
			\Mechanics\Server::out($actor, $mob->getAlias(true)."'s description now reads: ".$mob->getLong());
		}
		
		private function doLevel(\Mechanics\Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 3))
				return;
			
			$mob = $actor->getRoom()->getActorByInput($args[2]);
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "What mob do you want to grant a level to?");
			
			$num_levels = 1;
			if(sizeof($args) == 4)
				$num_levels = $args[3];
			
			if(!is_numeric($num_levels))
				return \Mechanics\Server::out($actor, "Number of levels granted must be a number.");
			
			$mob->setLevel($num_levels);
			$mob->save();
			
			return \Mechanics\Server::out($actor, "You grant ".$mob->getAlias()." ".$num_levels." level".($num_levels==1?'':'s'));
		}
		
		private function doInformation(\Mechanics\Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 3))
				return;
			
			array_shift($args);
			$mob = $actor->getRoom()->getActorByInput($args);
			
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "That's not a mob.");
			
			\Mechanics\Server::out($actor,
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
		
		private function doGold(\Mechanics\Actor $actor, $args)
		{
			$this->doWorth($actor, $args, 'gold');
		}
		
		private function doSilver(\Mechanics\Actor $actor, $args)
		{
			$this->doWorth($actor, $args, 'silver');
		}
		
		private function doCopper(\Mechanics\Actor $actor, $args)
		{
			$this->doWorth($actor, $args, 'copper');
		}
		
		private function doWorth(\Mechanics\Actor $actor, $args, $type)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$mob = $actor->getRoom()->getActorByInput($args[2]);
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "Mob not here.");
			
			$amount = $args[3];
			if(!is_numeric($amount) || $amount < 0 || $amount > 99999)
				return \Mechanics\Server::out($actor, "Invalid amount of ".$type." to give ".$mob->getAlias().".");
			
			$fn = 'set'.ucfirst($type).'Repop';
			$mob->$fn($amount);
			$fn = 'set'.ucfirst($type);
			$mob->$fn($amount);
			\Mechanics\Server::out($actor, "You set ".$mob->getAlias()."'s ".$type." amount to ".$amount.".");
		}
		
		private function doRespawn(\Mechanics\Actor $actor, $args)
		{
			$ticks = array_pop($args);
			if(!is_numeric($ticks))
				return \Mechanics\Server::out($actor, "What respawn time?");
			
			$mob = $actor->getRoom()->getActorByInput($args[2]);
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "Mob does not exist.");
			
			$mob->setDefaultRespawnTicks($ticks);
			\Mechanics\Server::out($actor, "You set ".$mob->getAlias()."'s respawn to ".$ticks." ticks.");
		}
		
		private function doSex(\Mechanics\Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$mob = $actor->getRoom()->getActorByInput($args[2]);
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "That's not a mob.");
			
			$sex = array_pop($args);
			if($mob->setSex($sex))
				return \Mechanics\Server::out($actor, $mob->getAlias(true)." is now a ".strtoupper($mob->getDisplaySex()).".");
		}
		
		private function doAutoflee(\Mechanics\Actor $actor, $args)
		{
			$auto_flee = array_pop($args);
			if(!is_numeric($auto_flee))
				return \Mechanics\Server::out($actor, "What respawn time?");
			
			$mob = $actor->getRoom()->getActorByInput($args[2]);
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "That's not a mob.");
			
			$mob->setAutoFlee($auto_flee);
			\Mechanics\Server::out($actor, $mob->getAlias(true)."'s auto flee is set to ".$auto_flee." hp.");
		}
		
		private function doMovement(\Mechanics\Actor $actor, $args)
		{
			$movement = array_pop($args);
			if(!is_numeric($movement))
				return \Mechanics\Server::out($actor, "What movement speed?");
			
			$mob = $actor->getRoom()->getActorByInput($args[2]);
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "That's not a mob.");
			
			$mob->setMovementTicks($movement);
			\Mechanics\Server::out($actor, $mob->getAlias()."'s movement speed set to ".$movement." ticks.");
		}
		
		private function doArea(\Mechanics\Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$mob = $actor->getRoom()->getActorByInput($args[2]);
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "That's not a mob.");
			
			$area = implode(' ', array_slice($args, 3));
			
			$mob->setArea($area);
			\Mechanics\Server::out($actor, $mob->getAlias(true)."'s area is now set to ".$area.".");
		}
		
		private function doHp(\Mechanics\Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$mob = $actor->getRoom()->getActorByInput($args[2]);
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "That's not a mob.");
			
			$mob->setHp($args[3]);
			$mob->setMaxHp($args[3]);
			\Mechanics\Server::out($actor, $mob->getAlias(true)."'s hp is now set to ".$args[3].".");
		}
		
		private function doMana(\Mechanics\Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$mob = $actor->getRoom()->getActorByInput($args[2]);
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "That's not a mob.");
			
			$mob->setMana($args[3]);
			$mob->setMaxMana($args[3]);
			\Mechanics\Server::out($actor, $mob->getAlias(true)."'s mana is now set to ".$args[3].".");
		}
		
		private function doMv(\Mechanics\Actor $actor, $args)
		{
			if(!$this->hasArgCount($actor, $args, 4))
				return;
			
			$mob = $actor->getRoom()->getActorByInput($args[2]);
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "That's not a mob.");
			
			$mob->setMovement($args[3]);
			$mob->setMaxMovement($args[3]);
			\Mechanics\Server::out($actor, $mob->getAlias(true)."'s movement points are now set to ".$args[3].".");
		}
		
		private function getCommand($arg)
		{
			$commands = array('race', 'level', 'hp', 'mana', 'mv', 'information', 'long', 'gold', 'silver', 'copper', 'respawn', 'movement', 'autoflee', 'sex', 'area');
			
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
