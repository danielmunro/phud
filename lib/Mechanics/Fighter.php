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
	namespace Mechanics;
	use \Items\Food,
		\Living\User,
		\Living\Mob,
		\Mechanics\Event\Subscriber,
		\Mechanics\Event\Event;

	abstract class Fighter extends Actor
	{
		const MAX_ATTRIBUTE = 25;
		
		protected $experience = 0;
		protected $experience_per_level = 0;
	
		public function getAttackSubscriber()
		{
			return new Subscriber(
				Event::EVENT_PULSE,
				$this,
				function($subscriber, $broadcaster, $fighter) {
					$target = $fighter->getTarget();
					if(empty($target) || !$fighter->isAlive()) {
						$subscriber->kill();
						return;
					}
					$fighter->fire(Event::EVENT_MELEE_ATTACK);
					$target->fire(Event::EVENT_MELEE_ATTACKED, $subscriber);
					if(!$subscriber->isSuppressed()) {
						$fighter->attack('Reg');
					}
				}
			);
		}

		public function getStatus()
		{
			$statuses = array
			(
				'100' => 'is in excellent condition',
				'99' => 'has a few scratches',
				'75' => 'has some small wounds and bruises',
				'50' => 'has quite a few wounds',
				'30' => 'has some big nasty wounds and scratches',
				'15' => 'looks pretty hurt',
				'0' => 'is in awful condition'
			);
			
			$hp_percent = ($this->getAttribute('hp') / $this->getMaxAttribute('hp')) * 100;
			$descriptor = '';
			foreach($statuses as $index => $status)
				if($hp_percent <= $index)
					$descriptor = $status;
			
			return $descriptor;
		
		}

		public function lookDescribe()
		{
			if($this->sex === 'm')
				$sex = 'him';
			else if($this->sex === 'f')
				$sex = 'her';
			
			if(!isset($sex))
				$sex = 'it';
			
			if(!$this->long)
				$this->long = 'You see nothing special about ' . $sex . '.';
			
			return  $this->long . "\r\n" . 
					ucfirst($this).' '.$this->getStatus().'.';
		
		}

		public function isAlive()
		{
			return $this->getAttribute('hp') > 0;
		}

		public function attack($attack_name = '', $verb = '')
		{
			$victim = $this->getTarget();
			if(!$victim) {
				return;
			}

			if(!$attack_name) {
				$attack_name = 'Reg';
			}

			$victim_target = $victim->getTarget();
			if(!$victim_target) {
				if($victim->reconcileTarget($this) === $this) {
					Server::instance()->addSubscriber($victim->getAttackSubscriber());
				}
			}
		
			$attacking_weapon = $this->getEquipped()->getEquipmentByPosition(Equipment::POSITION_WIELD);
			
			if($attacking_weapon['equipped']) {
				if(!$verb) {
					$verb = $attacking_weapon['equipped']->getVerb();
				}
				$dam_type = $attacking_weapon['equipped']->getDamageType();
			} else {
				if(!$verb) {
					$verb = $this->getRace()['lookup']->getUnarmedVerb();
				}
				$dam_type = Damage::TYPE_BASH;
			}
		
			// ATTACKING
			$hit_roll = $this->getHit();
			$dam_roll = $this->getDam();
			
			$hit_roll += ($this->getDex() / self::MAX_ATTRIBUTE) * 4;
			
			// DEFENDING
			$def_roll = ($victim->getDex() / self::MAX_ATTRIBUTE) * 4;
			
			// Size modifier
			$def_roll += 5 - $victim->getRace()['lookup']->getSize();
			
			if($dam_type == Damage::TYPE_BASH)
				$ac = $victim->getAcBash();
			elseif($dam_type == Damage::TYPE_PIERCE)
				$ac = $victim->getAcPierce();
			elseif($dam_type == Damage::TYPE_SLASH)
				$ac = $victim->getAcSlash();
			else
				$ac = $victim->getAcMagic();
			
			$ac = $ac / 100;	
			
			$roll['attack'] = rand(0, $hit_roll);
			$roll['defense'] = rand(0, $def_roll) - $ac;

			// Lost the hit roll -- miss
			if($roll['attack'] <= $roll['defense']) {
				$dam_roll = 0;
			} else {
				//(Primary Stat / 2) + (Weapon Skill * 4) + (Weapon Mastery * 3) + (ATR Enchantments) * 1.stance modifier
				//((Dexterity*2) + (Total Armor Defense*(Armor Skill * .03)) + (Shield Armor * (shield skill * .03)) + ((Primary Weapon Skill + Secondary Weapon Skill)/2)) * (1. Stance Modification)
				
				$modifier = 1;
				$this->fire(Event::EVENT_DAMAGE_MODIFIER_ATTACKING, $victim, $modifier, $dam_roll, $attacking_weapon);
				$victim->fire(Event::EVENT_DAMAGE_MODIFIER_DEFENDING, $this, $modifier, $dam_roll, $attacking_weapon);
				$dam_roll *= $modifier;
				$dam_roll = Server::_range(0, 200, $dam_roll);
				$victim->modifyAttribute('hp', -($dam_roll));
			}
			
			if($dam_roll < 5)
				$descriptor = 'clumsy';
			elseif($dam_roll < 10)
				$descriptor = 'amateur';
			elseif($dam_roll < 15)
				$descriptor = 'competent';
			else
				$descriptor = 'skillful';
			
			$actors = $this->getRoom()->getActors();
			foreach($actors as $a) {
				Server::out($a, ($a === $this ? 'Your' : ucfirst($this)."'s").' '.$descriptor.' '.$verb.' '.($dam_roll > 0 ? 'hits ' : 'misses ').($victim === $a ? 'you' : $victim) . '.');
			}

			if(!$victim->isAlive()) {
				$victim->afterDeath($this);
			}
		}
		
		public function reconcileTarget($args = array())
		{
			$actor_target = $this->getTarget();
			if(!$args)
				return $actor_target;
			
			if(is_array($args))
				$specified_target = $this->getRoom()->getActorByInput($args);
			else if($args instanceof self)
				$specified_target = $args;
				
			if($specified_target === $this)
			{
				return Server::out($this, "You can't target yourself!");
			}
			if(!$actor_target)
			{
				if(!$specified_target)
				{
					return Server::out($this, "No one is there.");
				}
				$this->target = $specified_target;
				return $specified_target;
			}
			else if(!($actor_target instanceof self))
				return Server::out($this, "I don't think they would like that very much.");
			else if($actor_target && !$specified_target)
				return $actor_target;
			else if($actor_target === $specified_target)
				return $actor_target;
			Server::out($this, "Whoa there sparky, don't you think one is enough?");
		}
		
		protected function afterDeath($killer)
		{
			$this->setTarget(null);
			$killer->setTarget(null);
		
			Debug::addDebugLine(ucfirst($killer).' killed '.$this.".");
			Server::out($killer, 'You have KILLED '.$this.'.');
			$killer->applyExperienceFrom($this);
			
			if($this instanceof User)
				$nouns = $this->getAlias();
			elseif($this instanceof Mob)
				$nouns = $this->getNouns();

			$gold = round($this->gold / 3);
			$silver = round($this->silver / 3);
			$copper = round($this->copper / 3);

			$corpse = new \Items\Corpse();
			$corpse->setLong('A corpse of '.$this.' lies here.');
			$corpse->setShort('a corpse of '.$this);
			$corpse->setNouns('corpse '.$nouns);
			$corpse->setWeight(100);
			$corpse->transferItemsFrom($this);
			
			$killer->addGold($gold);
			$killer->addSilver($silver);
			$killer->addCopper($copper);

			$this->gold = $gold;
			$this->silver = $silver;
			$this->copper = $copper;

			$corpse->addGold($gold);
			$corpse->addSilver($silver);
			$corpse->addCopper($copper);
			
			$this->getRoom()->announce($this, "You hear ".$this."'s death cry.");
			$r = round(rand(0, 3));
			if($r > 1) {
				$parts = [
					['brains', "'s brains splash all over you!"],
					['guts', ' spills '.$this->getDisplaySex().' guts all over the floor.'],
					['heart', "'s heart is torn from ".$this->getDisplaySex(). " chest."]
				];
				$r = round(rand(0, sizeof($parts)-1));
				$meat = new Food();
				$meat->setShort('the '.$parts[$r][0].' of '.$this);
				$meat->setLong('The '.$parts[$r][0].' of '.$this.' is here.');
				$meat->setNourishment(1);
				$this->getRoom()->addItem($meat);
				Server::out($killer, ucfirst($this).$parts[$r][1]);
			}
			$this->getRoom()->addItem($corpse);
							
			if($killer instanceof User) {
				Server::out($killer, "\n".$killer->prompt(), false);
			}
			
			$this->handleDeath();
		}
		
		protected function handleDeath()
		{
			Debug::addDebugLine(ucfirst($this).' died.');
			Server::out($this, 'You have been KILLED!');
		}
		
		public function applyExperienceFrom(Actor $victim)
		{
			if(!$this->experience_per_level) {
				// Mobs have 0 exp per level
				return 0;
			}
			
			Debug::addDebugLine("Applying experience from ".$victim." to ".$this.".");
			if($this->experience < $this->experience_per_level) {
				$experience = $victim->getKillExperience($this);
				$this->experience += $experience;
				Server::out($this, "You get ".$experience." experience for your kill.");
			}
		}
		
		public function getKillExperience(Fighter $killer)
		{
			$level_diff = $this->level - $killer->getLevel();
			
			switch($level_diff)
			{
				case -8:
					$base_exp = 2;
					break;
				case -7:
					$base_exp = 7;
					break;
				case -6:
					$base_exp = 13;
					break;
				case -5:
					$base_exp = 20;
					break;
				case -4:
					$base_exp = 26;
					break;
				case -3: 
					$base_exp = 40;
					break;
				case -2:
					$base_exp = 60;
					break;
				case -1:
					$base_exp = 80;
					break;
				case 0:
					$base_exp = 100;
					break;
				case 1:
					$base_exp = 140;
					break;
				case 2:
					$base_exp = 180;
					break;
				case 3:
					$base_exp = 220;
					break;
				case 4:
					$base_exp = 280;
					break;
				case 5:
					$base_exp = 320;
					break;
				default:
					$base_exp = 0;
					break;
			}
			
			if($level_diff > 5)
				$base_exp += 30 * $level_diff;
			
			$align_diff = abs($this->alignment - $killer->getAlignment()) / 2000;
			if($align_diff > 0.5)
			{
				$mod = rand(15, 35) / 100;
				$base_exp = $base_exp * (1 + ($align_diff - $mod));
			}
			
			$base_exp = rand($base_exp * 0.8, $base_exp * 1.2);
			return intval($base_exp);
		}
		
		protected function levelUp($display = true)
		{
			Debug::addDebugLine(ucfirst($this).' levels up.');
			$this->level++;
			$this->trains++;
			$this->practices += ceil($this->getWis() / 5);
			
			if($display) {
				Server::out($this, 'You LEVELED UP!');
				Server::out($this, 'Congratulations, you are now level ' . $this->level . '!');
			}
		}
		
		public function setExperience($experience)
		{
			$this->experience = $experience;
			if($this->experience <= 0) {
				$this->levelUp();
			}
		}
		
		public function awardExperience($experience)
		{
			$this->experience -= $experience;
			if($this->experience <= 0) {
				$this->levelUp();
			}
		}
		
		public function getExperience()
		{
			return $this->experience;
		}
		
		public function getExperiencePerLevel()
		{
			return $this->experience_per_level; 
		}
		
		public function setExperiencePerLevel($xp = 0)
		{
			if($xp < 1)
				$xp = $this->getExperiencePerLevelFromCP();
			$this->experience_per_level = $xp;
		}
		
		public function getExperiencePerLevelFromCP()
		{
			$cp = $this->getCreationPoints();
		
			if($cp < 30)
				return 1000;
		
			$base_mod = 100;
			if($cp < 99)
				return $cp * $base_mod;
			
			$upper_mod = 200;
			return (100 * $base_mod) + ($cp - 100 * $upper_mod);
		}
	}
?>
