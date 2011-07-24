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
	abstract class Fighter extends Actor
	{
	
		const MAX_ATTRIBUTE = 25;
		
		protected $experience = 0;
		protected $exp_per_level = 0;
		protected $concentration = 0;
		protected $delay = 0;
		protected $fightable = true;
		protected $attributes = null;
		protected $discipline = null;
		protected $battle = null;
		protected $target = null;
		protected $fighting = array();
	
		public function __construct($room_id)
		{
			$this->attributes = new Attributes();
			if($this->getId())
			{
				$this->attributes->load($this->getTable(), $this->getId());
			}
			else
			{
				$this->race->applyRacialAttributeModifiers($this);
			}
			parent::__construct($room_id);
		}
		public function getAttributes()
		{
			return $this->attributes;
		}
		public function getHp()
		{
			return $this->attributes->getHp();
		}
		public function getMaxHp()
		{
			$hp = $this->attributes->getMaxHp();
			foreach($this->affects as $a)
				$hp += $a->getAttributes()->getMaxHp();
			return $hp;
		}
		public function getMana()
		{
			return $this->attributes->getMana();
		}
		public function getMaxMana()
		{
			$mana = $this->attributes->getMaxMana();
			foreach($this->affects as $a)
				$mana += $a->getAttributes()->getMaxMana();
			return $mana;
		}
		public function getMovement()
		{
			return $this->attributes->getMovement();
		}
		public function getMaxMovement()
		{
			$movement = $this->attributes->getMaxMovement();
			foreach($this->affects as $a)
				$movement += $a->getAttributes()->getMaxMovement();
			return $movement;
		}
		public function setStr($str)
		{
			$this->attributes->setStr($str);
		}
		public function setInt($int)
		{
			$this->attributes->setInt($int);
		}
		public function setWis($wis)
		{
			$this->attributes->setWis($wis);
		}
		public function setDex($dex)
		{
			$this->attributes->setDex($dex);
		}
		public function setCon($con)
		{
			$this->attributes->setCon($con);
		}
		public function getStr()
		{
			$str = $this->attributes->getStr();
			foreach($this->affects as $a)
				$str += $a->getAttributes()->getStr();
			return $str;
		}
		public function getInt()
		{
			$int = $this->attributes->getInt();
			foreach($this->affects as $a)
				$int += $a->getAttributes()->getInt();
			return $int;
		}
		public function getWis()
		{
			$wis = $this->attributes->getWis();
			foreach($this->affects as $a)
				$wis += $a->getAttributes()->getWis();
			return $wis;
		}
		public function getDex()
		{
			$dex = $this->attributes->getDex();
			foreach($this->affects as $a)
				$dex += $a->getAttributes()->getDex();
			return $dex;
		}
		public function getCon()
		{
			$con = $this->attributes->getCon();
			foreach($this->affects as $a)
				$con += $a->getAttributes()->getCon();
			return $con;
		}
		
		public function getBaseStr()
		{
			return $this->attributes->getStr();
		}
		public function getBaseInt()
		{
			return $this->attributes->getInt();
		}
		public function getBaseWis()
		{
			return $this->attributes->getWis();
		}
		public function getBaseDex()
		{
			return $this->attributes->getDex();
		}
		public function getBaseCon()
		{
			return $this->attributes->getCon();
		}
		
		public function tick()
		{
			$this->attributes->setHp($this->attributes->getHp() + floor(rand($this->attributes->getMaxHp() * 0.05, $this->attributes->getMaxHp() * 0.1)));
			if($this->attributes->getHp() > $this->attributes->getMaxHp())
				$this->attributes->setHp($this->attributes->getMaxHp());
			$this->attributes->setMana($this->attributes->getMana() + floor(rand($this->attributes->getMaxMana() * 0.05, $this->attributes->getMaxMana() * 0.1)));
			if($this->attributes->getMana() > $this->attributes->getMaxMana())
				$this->attributes->setMana($this->attributes->getMaxMana());
			$this->attributes->setMovement($this->attributes->getMovement() + floor(rand($this->attributes->getMaxMovement() * 0.05, $this->attributes->getMaxMovement() * 0.1)));
			if($this->attributes->getMovement() > $this->attributes->getMaxMovement())
				$this->attributes->setMovement($this->attributes->getMaxMovement());
			parent::tick();
		}
		/**
		public function setRace($race)
		{
			parent::setRace($race);
			$this->race->applyRacialAttributeModifiers($this);
		}
		*/
		public function getExperience() { return $this->experience; }
		public function getExpPerLevel()
		{
			$abilities = array_merge($this->ability_set->getSkills(), $this->ability_set->getSpells());
			//$experience = "{$this->race}"::getBaseCreationCost();
			foreach($abilities as $ability)
				$experience += $this->discipline->getExperienceCost($ability);
			//return $this->exp_per_level;
			return 0;
		}
		public function getConcentration() { return $this->concentration; }
		public function getTarget() { return $this->target; }
		public function setTarget(Actor $target = null)
		{
			$this->target = $target;
		}
		public function getHpPercent()
		{
			return ($this->attributes->getHp() / $this->attributes->getMaxHp()) * 100;
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
			
			$hp_percent = $this->getHpPercent();
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
					$this->getAlias(true) . ' ' . $this->getStatus() . '.';
		
		}
		public function setHit($hit)
		{
			$this->attributes->setHit($hit);
		}
		public function setDam($dam)
		{
			$this->attributes->setDam($dam);
		}
		public function setAcSlash($ac_slash)
		{
			$this->attributes->setAcSlash($ac_slash);
		}
		public function setAcBash($ac_bash)
		{
			$this->attributes->setAcBash($ac_bash);
		}
		public function setAcPierce($ac_pierce)
		{
			$this->attributes->setAcPierce($ac_pierce);
		}
		public function setAcMagic($ac_magic)
		{
			$this->attributes->setAcMagic($ac_magic);
		}
		public function getAcSlash()
		{
			$ac = $this->attributes->getAcSlash();
			foreach($this->affects as $a)
				$ac += $a->getAttributes()->getAcSlash();
			return $ac;
		}
		public function getAcBash()
		{
			$ac = $this->attributes->getAcBash();
			foreach($this->affects as $a)
				$ac += $a->getAttributes()->getAcBash();
			return $ac;
		}
		public function getAcPierce()
		{
			$ac = $this->attributes->getAcPierce();
			foreach($this->affects as $a)
				$ac += $a->getAttributes()->getAcPierce();
			return $ac;
		}
		public function getAcMagic()
		{
			$ac = $this->attributes->getAcMagic();
			foreach($this->affects as $a)
				$ac += $a->getAttributes()->getAcMagic();
			return $ac;
		}
		public function isAlive()
		{
			if($this->attributes->getMaxHp() == 0)
				return true; // Creation
			return $this->attributes->getHp() > 0;
		}
		public function incrementDelay($delay)
		{
			$this->delay += $delay;
		}
		public function decrementDelay()
		{
			if($this->delay > 0)
				$this->delay--;
		}
		public function getDelay() { return $this->delay; }
		public function getFightable() { return $this->fightable; }
		public function setHp($hp)
		{
			$this->attributes->setHp($hp);
		}
		public function setMaxHp($max_hp)
		{
			$this->attributes->setHp($max_hp);
		}
		public function setMana($mana)
		{
			$this->attributes->setMana($mana);
		}
		public function setMaxMana($max_mana)
		{
			$this->attributes->setMovement($max_mana);
		}
		public function setMovement($movement)
		{
			$this->attributes->setMovement($movement);
		}
		public function setMaxMovement($max_movement)
		{
			$this->attributes->setMaxMovement($max_movement);
		}
		public function setExpPerLevel($exp) { $this->exp_per_level = $exp; }
		public function setExperience($experience)
		{
			$this->experience = $experience;
			if($this->experience <= 0)
				$this->levelUp();
		}
		public function awardExperience($experience)
		{
			$this->experience -= $experience;
			if($this->experience <= 0)
				$this->levelUp();
		}
		public function increaseHitDam($hit = 0, $dam = 0)
		{
			$this->attributes->setHit($this->attributes->getHit() + $hit);
			$this->attributes->setDam($this->attributes->getDam() + $dam);
		}
		public function decreaseHitDam($hit = 0, $dam = 0)
		{
			$this->attributes->setHit($this->attributes->getHit() - $hit);
			$this->attributes->setDam($this->attributes->getDam() - $dam);
		}
		public function getHit()
		{
			$hit = $this->attributes->getHit();
			foreach($this->affects as $a)
				$hit += $a->getAttributes()->getHit();
			return $hit;
		}
		public function getDam()
		{
			$dam = $this->attributes->getDam();
			foreach($this->affects as $a)
				$dam += $a->getAttributes()->getDam();
			return $dam;
		}
		public function decreaseConcentration() { $this->concentration--; if($this->concentration < 0) $this->concentration = 0; }
		public function increaseConcentration() { $this->concentration++; if($this->concentration > 10) $this->concentration = 10; }
		public function attack($actor = null)
		{
		
			if(!$actor)
				$actor = $this->getTarget();
			if(!$actor)
				return;
		
			Debug::addDebugLine("Battle round: " . $this->getAlias() . " attacking " . $actor->getAlias() . ". ", false);
			
			// Get necessary skills
			$ab_second = $this->ability_set->getLearnedAbility(\Skills\SecondAttack::instance());
			$ab_third = $this->ability_set->getLearnedAbility(\Skills\ThirdAttack::instance());
			
			$attacking_weapon = null;
			$hand_l = $this->getEquipped()->getEquipmentByPosition(Equipped::POSITION_WIELD_L);
			$hand_r = $this->getEquipped()->getEquipmentByPosition(Equipped::POSITION_WIELD_R);
			
			if($hand_l instanceof \Items\Weapon)
				$attacking_weapon = $hand_l;
			elseif($hand_r instanceof \Items\Weapon)
				$attacking_weapon = $hand_r;
			
			if($attacking_weapon)
			{
				$verb = $attacking_weapon->getVerb();
				$dam_type = $attacking_weapon->getDamageType();
			}
			else
			{
				$verb = $this->getRace()->getUnarmedVerb();
				$dam_type = Damage::TYPE_BASH;
			}
		
			// ATTACKING
			$hit_roll = $this->getHit();
			$dam_roll = $this->getDam();
			
			$hit_roll += ($this->getDex() / self::MAX_ATTRIBUTE) * 4;
			
			// DEFENDING
			$def_roll = ($actor->getDex() / self::MAX_ATTRIBUTE) * 4;
			
			// Size modifier
			$def_roll += 5 - $actor->getRace()->getSize();
			
			if($dam_type == Damage::TYPE_BASH)
				$ac = $actor->getAcBash();
			elseif($dam_type == Damage::TYPE_PIERCE)
				$ac = $actor->getAcPierce();
			elseif($dam_type == Damage::TYPE_SLASH)
				$ac = $actor->getAcSlash();
			else
				$ac = $actor->getAcMagic();
			
			$ac = $ac / 100;	
			
			$roll['attack'] = rand(0, $hit_roll);
			$roll['defense'] = rand(0, $def_roll) - $ac;
			
			// Lost the hit roll -- miss
			if($roll['attack'] <= $roll['defense'])
				$dam_roll = 0;
			
			if($dam_roll < 5)
				$descriptor = 'clumsy';
			elseif($dam_roll < 10)
				$descriptor = 'amateur';
			elseif($dam_roll < 15)
				$descriptor = 'competent';
			else
				$descriptor = 'skillful';
			
			//(Primary Stat / 2) + (Weapon Skill * 4) + (Weapon Mastery * 3) + (ATR Enchantments) * 1.stance modifier
			//((Dexterity*2) + (Total Armor Defense*(Armor Skill * .03)) + (Shield Armor * (shield skill * .03)) + ((Primary Weapon Skill + Secondary Weapon Skill)/2)) * (1. Stance Modification)
			
			$actors = $this->room->getActors();
			
			$attacks = 1;
			if($ab_second)
				$attacks++;
			if($ab_third)
				$attacks++;
			
			for($i = 0; $i < $attacks; $i++)
				if($this->damage($actor, $dam_roll))
					foreach($actors as $actor_sub)
						Server::out($actor_sub, ($actor_sub->getAlias() == $this->getAlias() ? 'Your' : $this->getAlias(true) . "'s") . ' ' . $descriptor . ' ' . $verb . ' ' . ($dam_roll > 0 ? 'hits ' : 'misses ') . ($actor->getAlias() == $actor_sub->getAlias() ? 'you' : $actor->getAlias()) . '.');
			
			$actor->checkAlive($this);
			Debug::addDebugLine(' Round done computing.');
		}
		public function damage(Actor &$target, $damage, $type = Damage::TYPE_HIT)
		{
		
			// Don't do anything if dead
			// Don't hit yerself
			// Check for safe rooms, imms, non mobs & non players, etc
			if(!$target->isAlive() || !$this->isAlive() || $this == $target || $target->isSafe())
				return false;
			
			// Damage reduction
			if ($damage > 35)
				$damage = ($damage - 35) / 2 + 35;
			if ($damage > 80)
				$damage = ($damage - 80) / 2 + 80;
			
			// Check for parry, dodge, and shield block
			if($type === Damage::TYPE_HIT)
			{
				$skill = $target->getAbilitySet()->getLearnedAbility(\Skills\Dodge::instance());
				if($skill && $skill->perform($target))
				{
					Server::out($this, $target->getAlias(true) . ' dodges your attack!');
					Server::out($target, 'You dodge ' . $this->getAlias() . "'s attack!");
					return false;
				}
				$skill = $target->getAbilitySet()->getLearnedAbility(\Skills\Shield_Block::instance());
				if($skill && $skill->perform($target))
				{
					
					Server::out($this, $target->getAlias(true) . " blocks your attack with " . $target->getDisplaySex() . " shield!");
					Server::out($target, "You block " . $this->getAlias() . "'s attack with your shield!");
					return false;
				}
			}
			
			$target->setHp($target->getHp() - $damage);
			return true;
			
		}
		public function checkAlive($killer = null)
		{
		
			if(!$this->isAlive())
			{
			
				$this->setTarget(null);
				$killer->setTarget(null);
			
				if($this->getAlias() != $killer->getAlias())
				{
					Debug::addDebugLine($killer->getAlias(true) . ' killed ' . $this->getAlias() . ".");
					Server::out($killer, 'You have KILLED ' . $this->getAlias() . '.');
					Server::out($killer, "You get " . $killer->applyExperienceFrom($this) . " experience for your kill.");
				}
				
				if($this instanceof \Living\User)
					$nouns = $this->getAlias();
				elseif($this instanceof \Living\Mob)
					$nouns = $this->getNoun();
				
				$corpse_inv = new Inventory('corpse', 0);
				$items = $this->inventory->getItems();
				foreach($items as $item)
				{
					$this->inventory->remove($item);
					$corpse_inv->add($item);
				}
				$corpse = new \Items\Container(0,
										'A corpse of ' . $this->getAlias() . ' lies here.',
										'a corpse of ' . $this->getAlias(),
										'corpse ' . $this->getAlias(),
										100,
										'corpse',
										$corpse_inv,
										false);
				$this->afterDeath($killer);
				$this->room->getInventory()->add($corpse);
				
				if($this instanceof \Living\User)
				{
					$this->inventory = new Inventory('users', $this->id);
					$this->inventory->save();
				}
				
				$this->handleDeath();
				return false;
			}
			return true;
		}
		
		protected function afterDeath($killer)
		{
			$r = ceil(rand(0, 3));
			if($r < 0)
			{
				return $this->getRoom()->announce($this, "You hear ".$this->getAlias()."'s death cry.");
			}
			else
			{
				$parts = array(
					'brains' => "'s brains splash all over you!",
					'guts' => ' spills '.$this->getDisplaySex().' guts all over the floor.',
					'heart' => "'s heart is torn from ".$this->getDisplaySex(). " chest."
				);
				$r = round(rand(0, sizeof($parts)-1));
				if($r == 1)
				{
					$this->getRoom()->getInventory()->add(new \Items\Food(0, 'The brains of '.$this->getAlias().' is here.', 'the brains of '.$this->getAlias(), 'brains', 0, 1, 1));
					\Mechanics\Server::out($killer, $this->getAlias(true).$parts['brains']);
				}
				else if($r == 2)
				{
					$this->getRoom()->getInventory()->add(new \Items\Food(0, 'The entrails of '.$this->getAlias().' is here.', 'the entrails of '.$this->getAlias(), 'entrails', 0, 1, 1));
					\Mechanics\Server::out($killer, $this->getAlias(true).$parts['guts']);
				}
				else if($r == 3)
				{
					$this->getRoom()->getInventory()->add(new \Items\Food(0, 'The heart of '.$this->getAlias().' is here.', 'the heart of '.$this->getAlias(), 'heart', 0, 1, 1));
					\Mechanics\Server::out($killer, $this->getAlias(true).$parts['heart']);
				}
			}
		}
		
		protected function handleDeath($move_soul = true)
		{
			if($move_soul)
				$this->setRoom(Room::find(1));
			$this->setHp(1);
			Debug::addDebugLine($this->getAlias(true) . ' died.');
			Server::out($this, 'You have been KILLED!');
		}
		
		public function initiateBattle(Actor &$actor)
		{
			if($actor == $this)
				return false;
			$this->setTarget($actor);
			if($actor->getBattle())
				return $actor->getBattle()->addActor($this);
			$this->setBattle(new Battle($this));
		}
		
		public function setBattle(Battle &$battle)
		{
			$this->battle = $battle;
		}
		
		public function getBattle()
		{
			return $this->battle;
		}
		
		public function applyExperienceFrom(Actor $actor)
		{
			if(!$this->exp_per_level) // Mobs have 0 exp per level
				return 0;
			Debug::addDebugLine("Applying experience from " . $actor->getAlias() . ' to ' . $this->getAlias() . '.');
			$experience = $actor->getKillExperience();
			$level_diff = $this->level - $actor->getLevel();
			
			if($level_diff > 5)
				$experience *= 1.3;
			else if($level_diff > 3)
				$experience *= 1.2;
			else if($level_diff > 0)
				$experience *= 1.1;
			else if($level_diff < 0)
				$experience *= 0.75;
			else if($level_diff < -3)
				$experience *= 0.25;
			else
				$experience *= 0.1;
			
			$randomizer_percent = rand(0, 10);
			$randomizer_coin = rand(0, 1);
			$randomizer_mod = $experience * ($randomizer_percent / 100);
			if($randomizer_coin)
				$experience += $randomizer_mod;
			else
				$experience -= $randomizer_mod;
			
			$experience = (int) $experience;
			
			$this->experience += $experience;
			
			$diff = (int) ($this->experience / $this->exp_per_level);
			if($diff > $this->level)
				$this->levelUp();
			
			return $experience;
		}
		public function getKillExperience()
		{
			return 300 + (10 * $this->level);
		}
		protected function levelUp($display = true)
		{
			Debug::addDebugLine($this->getAlias(true) . ' levels up.');
			$hp_gain = ceil($this->con * 0.5);
			$movement_gain = ceil(($this->con * 0.6) + ($this->dex * 0.9) / 1.5);
			$mana_gain = ceil(($this->wis + $this->int / 2) * 0.8);
			
			$this->max_hp += (int) $hp_gain;
			$this->max_mana += (int) $mana_gain;
			$this->max_movement += (int) $movement_gain;
			
			$this->level = (int) ($this->experience / $this->exp_per_level);
			
			if($display)
			{
				Server::out($this, 'You LEVELED UP!');
				Server::out($this, 'Congratulations, you are now level ' . $this->level . '!');
			}
		}
	}
?>
