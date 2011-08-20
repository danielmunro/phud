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
	class Mob extends \Mechanics\Command
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
		
		private function doCreate(\Mechanics\Actor $actor, $args)
		{
			if(sizeof($args) <= 2)
				return \Mechanics\Server::out($actor, "You need to specify a name for your new mob.");
			
			$alias = implode(' ', array_slice($args, 2));
			$nouns = substr($alias, strrpos($alias, ',')+1);
			$alias = substr($alias, 0, strrpos($alias, ','));
			
			if(!\Living\Mob::validateAlias($alias))
				return \Mechanics\Server::out($actor, "\"".$alias."\" is not a valid name for a mob.");
			
			$mob = new \Living\Mob();
			$mob->setAlias($alias);
			$mob->setNouns($nouns);
			$mob->setRoom($actor->getRoom());
			$mob->setStartRoom();
			$mob->save();
			
			$mob->getRoom()->announce($mob, $mob->getAlias(true)." poofs into existence.");
		}
		
		private function doRace(\Mechanics\Actor $actor, $args)
		{
			if(sizeof($args) < 4)
				return \Mechanics\Server::out($actor, "You are missing something.");
			
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
			if(sizeof($args) <= 3)
				return \Mechanics\Server::out($actor, "You are missing some arguments.");
			
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
			if(sizeof($args) <= 2)
				return \Mechanics\Server::out($actor, "What was that?");
			
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
			if(sizeof($args) <= 2)
				return \Mechanics\Server::out($actor, "Which mob?");
			
			array_shift($args);
			$mob = $actor->getRoom()->getActorByInput($args);
			
			if(!($mob instanceof \Living\Mob))
				return \Mechanics\Server::out($actor, "That's not a mob.");
			
			\Mechanics\Server::out($actor,
					"info page on mob ".$mob->getId().":\n".
					"alias:                    ".$mob->getAlias()."\n".
					"race:                     ".$mob->getRace()->getAlias()."\n".
					"level:                    ".$mob->getLevel()."\n".
					"nouns:                    ".$mob->getNouns()."\n".
					"stats:                    ".$mob->getHp().'/'.$mob->getMaxHp().'hp '.$mob->getMana().'/'.$mob->getMaxMana().'m '.$mob->getMovement().'/'.$mob->getMaxMovement()."\n".
					"max worth:                ".$mob->getGold().'g '.$mob->getSilver().'s '.$mob->getCopper()."c\n".
					"long:\n".
					($mob->getLong() ? $mob->getLong() : "Nothing."));
		}
		
		private function doGold()
		{
			
		}
		
		private function doSilver()
		{
		}
		
		private function doCopper()
		{
		}
		
		private function getCommand($arg)
		{
			$commands = array('create', 'race', 'level', 'information', 'long', 'gold', 'silver', 'copper');
			
			$command = array_filter($commands, function($c) use ($arg) 
				{
					return strpos($c, $arg) === 0;
				});
			
			if(sizeof($command))
				return array_shift($command);
			
			return false;
		}
	}
?>
