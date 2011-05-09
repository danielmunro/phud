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
		protected $hit_roll = 0;
		protected $dam_roll = 0;
		
		// Armor
		protected $ac_slash = 0;
		protected $ac_bash = 0;
		protected $ac_pierce = 0;
		protected $ac_magic = 0;
		
		protected $discipline = null;
		protected $battle = null;
		protected $target = null;
		protected $fighting = array();
		
		// Vitals
		protected $hp = 20;
		protected $max_hp = 20;
		protected $mana = 100;
		protected $max_mana = 100;
		protected $movement = 100;
		protected $max_movement = 100;
		
		// Attributes
		protected $str = 0;
		protected $_int = 0;
		protected $wis = 0;
		protected $dex = 0;
		protected $con = 0;
		protected $vit = 0;
		protected $wil = 0;
	
		public function __construct($room_id)
		{
			$this->hit_roll = $this->race->getHitRoll();
			$this->dam_roll = $this->race->getDamRoll();
			parent::__construct($room_id);
		}
		
		public function getHp() { return $this->hp; }
		public function getMaxHp() { return $this->max_hp; }
		public function getMana() { return $this->mana; }
		public function getMaxMana() { return $this->max_mana; }
		public function getMovement() { return $this->movement; }
		public function getMaxMovement() { return $this->max_movement; }
		public function getStr($base = false)
		{
			if($base)
				return $this->str;
			$str = $this->str;
			if(array_key_exists('stun', $this->affects))
				$str -= 3;
			if(array_key_exists('berserk', $this->affects))
				$str += $this->affects['berserk']->getArgs('str');
			return $str;
		}
		public function getInt($base = false)
		{
			if($base)
				return $this->_int;
			$int = $this->_int;
			return $int;
		}
		public function getWis($base = false)
		{
			if($base)
				return $this->wis;
			$wis = $this->wis;
			return $wis;
		}
		public function getDex($base = false)
		{
			if($base)
				return $this->dex;
			$dex = $this->dex;
			if(array_key_exists('berserk', $this->affects))
				$dex += $this->affects['berserk']->getArgs('dex');
			return $dex;
		}
		public function getCon($base = false)
		{
			if($base)
				return $this->con;
			$con = $this->con;
			return $con;
		}
		public function getVit($base = false)
		{
			if($base)
				return $this->vit;
			$vit = $this->vit;
			return $vit;
		}
		public function getWil($base = false)
		{
			if($base)
				return $this->wil;
			$wil = $this->wil;
			return $wil;
		}
		public function setStr($str) { $this->str = $str; }
		public function setInt($int) { $this->_int = $int; }
		public function setWis($wis) { $this->wis = $wis; }
		public function setDex($dex) { $this->dex = $dex; }
		public function setCon($con) { $this->con = $con }
		public function setVit($vit) { $this->vil = $vit; }
		public function setWil($wil) { $this->wil = $wil; }
		public function tick()
		{
			$this->hp += floor(rand($this->max_hp * 0.05, $this->max_hp * 0.1));
			if($this->hp > $this->max_hp)
				$this->hp = $this->max_hp;
			$this->mana += floor(rand($this->max_mana * 0.05, $this->max_mana * 0.1));
			if($this->mana > $this->max_mana)
				$this->mana = $this->max_mana;
			$this->movement += floor(rand($this->max_movement * 0.05, $this->max_movement * 0.1));
			if($this->movement > $this->max_movement)
				$this->movement = $this->max_movement;
			parent::tick();
		}
		public function setRace($race)
		{
			parent::setRace($race);
			$this->race->applyRacialAttributeModifiers($this);
		}
		public function getExperience() { return $this->experience; }
		public function getExpPerLevel() { return $this->exp_per_level; }
		public function getConcentration() { return $this->concentration; }
		public function getTarget() { return $this->target; }
		public function setTarget(Actor $target = null)
		{
			$this->target = $target;
		}
		public function getHpPercent()
		{
			return ($this->hp / $this->max_hp) * 100;
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
		public function setHitRoll($hit_roll) { $this->hit_roll = $hit_roll; }
		public function setDamRoll($dam_roll) { $this->dam_roll = $dam_roll; }
		public function setAcSlash($ac_slash) { $this->ac_slash = $ac_slash; }
		public function setAcBash($ac_bash) { $this->ac_bash = $ac_bash; }
		public function setAcPierce($ac_pierce) { $this->ac_pierce = $ac_pierce; }
		public function setAcMagic($ac_magic) { $this->ac_magic = $ac_magic; }
		public function getAcSlash() { return $this->getAc('ac_slash'); }
		public function getAcBash() { return $this->getAc('ac_bash'); }
		public function getAcPierce() { return $this->getAc('ac_pierce'); }
		public function getAcMagic() { return $this->getAc('ac_magic'); }
		private function getAc($ac_class)
		{
			$ac = $this->$ac_class;
			if(array_key_exists('armor', $this->affects))
				$ac += $this->affects['armor']->getArgs('mod_ac');
			return $ac;
		}
		public function isAlive()
		{
			if($this->max_hp == 0)
				return true; // Creation
			return $this->hp > 0;
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
		public function setHp($hp, $killer = null) { $this->hp = $hp; }
		public function setMaxHp($max_hp) { $this->max_hp = $max_hp; }
		public function setMana($mana) { $this->mana = $mana; }
		public function setMaxMana($max_mana) { $this->max_mana = $max_mana; }
		public function setMovement($movement) { $this->movement = $movement; }
		public function setMaxMovement($max_movement) { $this->max_movement = $max_movement; }
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
			$this->hit_roll += $hit;
			$this->dam_roll += $dam;
		}
		public function decreaseHitDam($hit = 0, $dam = 0)
		{
			$this->hit_roll -= $hit;
			$this->dam_roll -= $dam;
		}
		public function decreaseConcentration() { $this->concentration--; if($this->concentration < 0) $this->concentration = 0; }
		public function increaseConcentration() { $this->concentration++; if($this->concentration > 10) $this->concentration = 10; }
		public function attack($actor = null)
		{
		
			if(!$actor)
				$actor = $this->getTarget();
			if(!$actor)
				return;
		
			$this->decrementDelay();
			Debug::addDebugLine("Battle round: " . $this->getAlias() . " attacking " . $actor->getAlias() . ". ", false);
			
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
			$hit_roll = $this->hit_roll;
			$dam_roll = $this->dam_roll;
			
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
			
			if($this->damage($actor, $dam_roll))
				foreach($actors as $actor_sub)
					Server::out($actor_sub, ($actor_sub->getAlias() == $this->getAlias() ? 'Your' : $this->getAlias(true) . "'s") . ' ' . $descriptor . ' ' . $verb . ' ' . ($dam_roll > 0 ? 'hits ' : 'misses ') . ($actor->getAlias() == $actor_sub->getAlias() ? 'you' : $actor->getAlias()) . '.');
			
			$actor->checkAlive($this);
			Debug::addDebugLine(' Round done computing.');
		}
		public function damage(Actor &$target, $damage, $type = Damage::TYPE_HIT)
		{
		
			// Don't do anything if dead
			if(!$target->isAlive() || !$this->isAlive())
				return false;
			
			// Don't hit yerself
			if($this->getAlias() == $target->getAlias())
				return false;
			
			// Damage reduction
			if ($damage > 35)
				$damage = ($damage - 35) / 2 + 35;
			if ($damage > 80)
				$damage = ($damage - 80) / 2 + 80;
			
			// Check for safe rooms, imms, non mobs & non players, etc
			if($target->isSafe())
				return false;
			
			// Check for parry, dodge, and shield block
			if($type === Damage::TYPE_HIT)
			{
				$skill = $target->getAbilitySet()->isValidSkill('dodge');
				if($skill && $skill->perform($target))
				{
					Server::out($this, $target->getAlias(true) . ' dodges your attack!');
					Server::out($target, 'You dodge ' . $this->getAlias() . "'s attack!");
					return false;
				}
				$skill = $target->getAbilitySet()->isValidSkill('shield block');
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
				
				$corpse = new \Items\Container(0,
										'A corpse of ' . $this->getAlias() . ' lies here.',
										'a corpse of ' . $this->getAlias(),
										'corpse ' . $this->getAlias(),
										100,
										'corpse',
										$this->inventory,
										false);
				
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
