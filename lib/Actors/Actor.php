<?php
namespace Phud\Actors;
use Phud\Abilities\Ability,
	Phud\Abilities\Skill,
	Phud\Affects\Affectable,
	Phud\Inventory,
	Phud\Server,
	Phud\Interactive,
	Phud\Races\Race,
	Phud\Room,
	Phud\Equipped,
	Phud\Attributes,
	Phud\EasyInit,
	Phud\Listener,
	Phud\Identity,
	Phud\Damage,
	Phud\Debug,
	Phud\Items\Corpse,
	Phud\Items\Food,
	Phud\Items\Furniture,
	Phud\Items\Equipment;

abstract class Actor
{
	use Affectable, Listener, Inventory, Interactive, EasyInit, Identity;

	const MAX_LEVEL = 51;
	
	const DISPOSITION_STANDING = 'standing';
	const DISPOSITION_SITTING = 'sitting';
	const DISPOSITION_SLEEPING = 'sleeping';
	
	const SEX_NEUTRAL = 1;
	const SEX_FEMALE = 2;
	const SEX_MALE = 3;

	const MAX_ATTRIBUTE = 25;
	
	protected $level = 0;
	protected $gold = 0;
	protected $silver = 0;
	protected $copper = 0;
	protected $sex = self::SEX_NEUTRAL;
	protected $disposition = self::DISPOSITION_STANDING;
	protected $race = 'critter';
	protected $race_listeners = [];
	protected $room = null;
	protected $equipped = null;
	protected $alignment = 0;
	protected $attributes = null;
	protected $max_attributes = null;
	protected $abilities = [];
	protected $target = null;
	protected $experience = 0;
	protected $experience_per_level = 0;
	protected $furniture = null;
	protected $is_alive = true;
	protected $proficiencies = [
		'stealth' => 15,
		'healing' => 15,
		'one handed weapons' => 15,
		'two handed weapons' => 15,
		'leather armor' => 15,
		'chain armor' => 15,
		'plate armor' => 15,
		'melee' => 15,
		'evasive' => 15,
		'archery' => 15,
		'alchemy' => 15,
		'elemental' => 15,
		'illusion' => 15,
		'transportation' => 15,
		'sorcery' => 15,
		'maladictions' => 15,
		'benedictions' => 15,
		'curative' => 15,
		'beguiling' => 15,
		'speech' => 15
	];
	
	public function __construct($properties = [])
	{
		// set generic attribute values
		$this->attributes = new Attributes([
			'str' => 15,
			'int' => 15,
			'wis' => 15,
			'dex' => 15,
			'con' => 15,
			'cha' => 15,
			'hp' => 20,
			'mana' => 100,
			'movement' => 100,
			'ac_bash' => 100,
			'ac_slash' => 100,
			'ac_pierce' => 100,
			'ac_magic' => 100,
			'hit' => 1,
			'dam' => 1,
			'saves' => 100
		]);

		// do the EasyInit initializer
		$this->initializeProperties($properties, [
			'attributes' => function($actor, $property, $value) {
				foreach($value as $attr => $attr_value) {
					$actor->setAttribute($attr, $attr_value);
				}
			},
			'abilities' => function($actor, $property, $value) {
				foreach($value as $ability) {
					$actor->addAbility($ability);
				}
			}
		]);

		// set the max attributes based on the existing attributes
		$this->max_attributes = new Attributes([
			'str' => $this->attributes->getAttribute('str') + 4,
			'int' => $this->attributes->getAttribute('int') + 4,
			'wis' => $this->attributes->getAttribute('wis') + 4,
			'dex' => $this->attributes->getAttribute('dex') + 4,
			'con' => $this->attributes->getAttribute('con') + 4,
			'cha' => $this->attributes->getAttribute('cha') + 4,
			'hp' => $this->attributes->getAttribute('hp'),
			'mana' => $this->attributes->getAttribute('mana'),
			'movement' => $this->attributes->getAttribute('movement')
		]);

		// apply any racial modifiers
		$this->setRace(Race::lookup($this->race));

		// create equipment object
		$this->equipped = new Equipped($this);

		// initialized identity
		if($this->id) {
			self::$identities[$this->id] = $this;
		}

		$this->applyListeners();
	}

	///////////////////////////////////////////////////////////////////
	// Ability functions
	///////////////////////////////////////////////////////////////////

	public function getAbilities()
	{
		return $this->abilities;
	}

	public function addAbility($ability)
	{
		$this->abilities[] = $ability['alias'];
		if($ability['lookup'] instanceof Skill) {
			$listener = $ability['lookup']->getListener();
			$this->on($listener[0], $listener[1]);
		}
	}

	public function removeAbility($ability)
	{
		$alias = $ability['alias'];
		if(isset($this->ability[$alias])) {
			unset($this->ability[$alias]);
			if($ability['lookup'] instanceof Skill) {
				$ability['lookup']->removeListener($this);
			}
		}
	}

	///////////////////////////////////////////////////////////////////
	// Attributes functions
	///////////////////////////////////////////////////////////////////

	public function getMaxAttribute($key)
	{
		return $this->max_attributes->getAttribute($key);
	}

	public function getUnmodifiedAttribute($key)
	{
		return $this->attributes->getAttribute($key);
	}

	public function getAttribute($key)
	{
		$n = $this->attributes->getAttribute($key);
		foreach($this->affects as $affect) {
			$n += $affect->getAttribute($key);
		}
		foreach($this->equipped->getItems() as $eq) {
			$n += $eq->getAttribute($key);
			$affs = $eq->getAffects();
			foreach($affs as $aff) {
				$n += $aff->getAttribute($key);
			}
		}
		$n += $this->race['lookup']->getAttribute($key);
		$max = $this->max_attributes->getAttribute($key);
		$n = round($n);
		return $max > 0 ? min($n, $this->max_attributes->getAttribute($key)) : $n;
	}

	public function modifyAttribute($key, $amount)
	{
		$this->attributes->modifyAttribute($key, $amount);
		$this->fire('mod_'.$key, $amount);
	}

	public function setAttribute($key, $amount)
	{
		$this->attributes->setAttribute($key, $amount);
		$this->fire('mod_'.$key, $amount);
	}

	///////////////////////////////////////////////////////////////////////////
	// Money functions
	///////////////////////////////////////////////////////////////////////////

	private function isCurrency($currency)
	{
		return $currency === 'copper' || $currency === 'silver' || $currency === 'gold';
	}

	public function getCurrency($currency)
	{
		if($this->isCurrency($currency)) {
			return $this->$currency;
		} else {
			Debug::log("[".$currency."] is not a valid currency type.");
		}
	}

	public function modifyCurrency($currency, $amount)
	{
		if($this->isCurrency($currency)) {
			$this->$currency += $amount;
		}
	}

	public function getWorth()
	{
		return $this->copper + ($this->silver * 100) + ($this->gold * 1000);
	}

	public function decreaseFunds($copper)
	{
		$copper = abs($copper);
		if($this->getWorth() < $copper) {
			return false; // Not enough money
		}

		$this->copper -= $copper; // Remove the money

		// ensure that copper amount stays above zero
		while($this->copper < 0) {
			if($this->silver > 0) {
				$this->silver--;
				$this->copper += 100;
				continue;
			}
			if($this->gold > 0) {
				$this->gold--;
				$this->copper += 1000;
			}
		}
	}

	///////////////////////////////////////////////////////////////////////////
	// Fighting methods
	///////////////////////////////////////////////////////////////////////////

	public function getTarget()
	{
		return $this->target;
	}

	public function setTarget(Actor $target = null)
	{
		$this->target = $target;
		if($this->target) {
			$fighter = $this;
			Server::instance()->on(
				'pulse',
				function($event, $server) use ($fighter) {
					$target = $fighter->getTarget();
					if(empty($target) || !$fighter->isAlive()) {
						$event->kill();
						return;
					}
					$e = $target->fire('attacked');
					if($e->getStatus() === 'satisfied') {
						return;
					} else if($e->getStatus() === 'killed') {
						$event->kill();
						return;
					}
					$fighter->fire('attack');
				}
			);
		}
	}

	public function reconcileTarget($args = [])
	{
		if(sizeof($args) <= 1) {
			return $this->target;
		}

		$specified_target = is_array($args) ? $this->getRoom()->getActorByInput(array_slice($args, -1)[0]) : $args;

		if(empty($this->target)) {
			if(empty($specified_target)) {
				return Server::out($this, "No one is there.");
			}
			if(!($specified_target instanceof self)) {
				return Server::out($this, "I don't think they would like that very much.");
			}
			if($this === $specified_target) {
				return Server::out($this, "You can't target yourself!");
			}
			$this->setTarget($specified_target);
		} else if(!empty($specified_target) && $this->target !== $specified_target) {
			return Server::out($this, "Whoa there sparky, don't you think one is enough?");
		}
		return $this->target;
	}

	public function attack($attack_name = '', $verb = '')
	{
		$victim = $this->getTarget();
		if(!$victim) {
			return;
		}

		$victim->fire('hit', $this);

		if(!$attack_name) {
			$attack_name = 'Reg';
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
		$hit_roll = $this->getAttribute('hit');
		$dam_roll = $this->getAttribute('dam');
		$hit_roll += ($this->getAttribute('dex') / self::MAX_ATTRIBUTE) * 4;

		// DEFENDING
		$def_roll = ($victim->getAttribute('dex') / self::MAX_ATTRIBUTE) * 4;

		// Size modifier
		$def_roll += 5 - $victim->getRace()['lookup']->getSize();

		$ac = 0;
		if($dam_type === Damage::TYPE_BASH)
			$ac = $victim->getAttribute('ac_bash');
		else if($dam_type === Damage::TYPE_PIERCE)
			$ac = $victim->getAttribute('ac_pierce');
		else if($dam_type === Damage::TYPE_SLASH)
			$ac = $victim->getAttribute('ac_slash');
		else if($dam_type === Damage::TYPE_MAGIC)
			$ac = $victim->getAttribute('ac_magic');

		$ac = $ac / 100;	

		$roll['attack'] = rand(0, $hit_roll);
		$roll['defense'] = rand(0, $def_roll) - $ac;

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
			Server::out($a, ($a === $this ? '('.$attack_name.') Your' : ucfirst($this)."'s").' '.$descriptor.' '.$verb.' '.($dam_roll > 0 ? 'hits ' : 'misses ').($victim === $a ? 'you' : $victim) . '.');
		}

		// Lost the hit roll -- miss
		if($roll['attack'] <= $roll['defense']) {
			$dam_roll = 0;
		} else {
			//(Primary Stat / 2) + (Weapon Skill * 4) + (Weapon Mastery * 3) + (ATR Enchantments) * 1.stance modifier
			//((Dexterity*2) + (Total Armor Defense*(Armor Skill * .03)) + (Shield Armor * (shield skill * .03)) + ((Primary Weapon Skill + Secondary Weapon Skill)/2)) * (1. Stance Modification)

			$modifier = 1;
			$this->fire('damage modifier', $victim, $modifier, $dam_roll, $attacking_weapon);
			$victim->fire('defense modifier', $this, $modifier, $dam_roll, $attacking_weapon);
			$dam_roll *= $modifier;
			$dam_roll = _range(0, 200, $dam_roll);
			$victim->modifyAttribute('hp', -($dam_roll));
		}

		if($victim->getAttribute('hp') < 1) {
			$victim->setTarget(null);
			$this->setTarget(null);

			Debug::log(ucfirst($this).' killed '.$victim.".");
			Server::out($this, 'You have KILLED '.$victim.'.');
			$this->applyExperienceFrom($victim);

			$gold = round($victim->getCurrency('gold') / 2);
			$silver = round($victim->getCurrency('silver') / 2);
			$copper = round($victim->getCurrency('copper') / 2);

			$victim->modifyCurrency('gold', -$gold);
			$victim->modifyCurrency('silver', -$silver);
			$victim->modifyCurrency('copper', -$copper);

			$this->gold += $gold;
			$this->silver += $silver;
			$this->copper += $copper;

			$this->getRoom()->announce($victim, "You hear ".$victim."'s death cry.");
			if(chance() < 25) {
				$s = $victim->getDisplaySex();
				$parts = $victim->getRace()['lookup']->getParts();
				$custom_message = [
					['brains' => ucfirst($victim)."'s brains splash all over you!"],
					['guts' => ucfirst($victim).' spills '.$s.' guts all over the floor.'],
					['heart' => ucfirst($victim)."'s heart is torn from ".$s." chest."]
				];
				$k = array_rand($parts);
				if(isset($custom_message[$parts[$k]])) {
					$message = $custom_message[$parts[$k]];
				} else {
					$message = ucfirst($victim)."'s ".$parts[$k].' is sliced from '.$s.' body.';
				}
				$this->getRoom()->announce([
					['actor' => '*', 'message' => $message]
				]);
				$this->getRoom()->addItem(new Food([
					'short' => 'the '.$parts[$k].' of '.$victim,
					'long' => 'The '.$parts[$k].' of '.$victim.' is here.',
					'nourishment' => 5
				]));
			}
			
			if($this instanceof User) {
				Server::out($this, "\n".$this->prompt(), false);
			}

			Debug::log(ucfirst($victim).' died.');
			Server::out($victim, 'You have been KILLED!');
		}
	}
	
	public function death()
	{
		$corpse = new Corpse([
			'short' => 'the corpse of '.$this,
			'long' => 'The corpse of '.$this.' lies here.',
			'weight' => 100
		]);
		foreach($this->items as $i) {
			$this->removeItem($i);
			$corpse->addItem($i);
		}
		$this->getRoom()->addItem($corpse);
		$this->is_alive = false;
		$this->fire('died');
	}

	public function tick()
	{
		$amount = rand(0.05, 0.1);
		$modifier = 1;
		$this->fire('tick', $amount, $modifier);
		$amount *= $modifier;
		foreach(['hp', 'mana', 'movement'] as $att) {
			$this->modifyAttribute($att, round($amount * $this->getAttribute($att)));
		}
	}

	public function getProficiencyIn($proficiency)
	{
		if(!isset($this->proficiencies[$proficiency])) {
			Debug::log("Error, proficiency not defined: ".$proficiency);
			$this->proficiencies[$proficiency] = 15;
		}
		return $this->proficiencies[$proficiency];
	}

	public function getFurniture()
	{
		return $this->furniture;
	}

	public function setFurniture(Furniture $furniture = null)
	{
		if($this->furniture) {
			$this->furniture->removeActor($this);
		}
		$this->furniture = $furniture;
	}

	public function getAlignment()
	{
		return $this->alignment;
	}

	public function modifyAlignment($alignment)
	{
		$this->alignment += $alignment;
	}

	public function getDisposition()
	{
		return $this->disposition;
	}

	public function setDisposition($disposition)
	{
		$this->disposition = $disposition;
	}

	public function getLong()
	{
		return $this->long ? $this->long : 'You see nothing special about '.$this->getDisplaySex([self::SEX_MALE => 'him', self::SEX_FEMALE => 'her', self::SEX_NEUTRAL => 'it']).'.';
	}

	public function getEquipped()
	{
		return $this->equipped;
	}

	public function getSex()
	{
		return $this->sex;
	}

	public function getDisplaySex($set = [])
	{
		if(empty($set)) {
			$set = [self::SEX_MALE=>'his', self::SEX_FEMALE=>'her', self::SEX_NEUTRAL=>'its'];
		}
		if(isset($set[$this->sex])) {
			return $set[$this->sex];
		}
		return 'its';
	}

	public function setSex($sex)
	{
		if($sex === self::SEX_FEMALE || $sex === self::SEX_MALE || $sex === self::SEX_NEUTRAL) {
			$this->sex = $sex;
		}
	}

	public function setRoom(Room $room)
	{
		if($this->room) {
			$this->room->actorRemove($this);
		}
		$room->actorAdd($this);
		$this->room = $room;
	}

	public function getRoom()
	{
		return $this->room;
	}

	public function getRace()
	{
		return $this->race;
	}

	public function setRace($race)
	{
		if(isset($this->race['lookup']) && is_object($this->race['lookup'])) {
			// Undo all previous racial listeners/abilities/stats/proficiencies
			foreach($this->race_listeners as $listener) {
				$this->unlisten($listener[0], $listener[1]);
			}
			foreach($this->race['lookup']->getProficiencies() as $proficiency => $amount) {
				$this->proficiencies[$proficiency] -= $amount;
			}
			foreach($this->race['lookup']->getAbilities() as $ability_alias) {
				$ability = Ability::lookup($ability_alias);
				$this->removeAbility($ability);
			}
		}

		// Assign all racial listeners/abilities/stats/proficiencies
		$this->race = $race;
		$this->race_listeners = $race['lookup']->getListeners();
		foreach($this->race_listeners as $listener) {
			$this->on($listener[0], $listener[1]);
		}
		$profs = $race['lookup']->getProficiencies();
		foreach($profs as $name => $value) {
			$this->proficiencies[$name] += $value;
		}
		foreach($race['lookup']->getAbilities() as $ability_alias) {
			$ability = Ability::lookup($ability_alias);
			$this->addAbility($ability);
		}
	}

	public function levelUp()
	{
		Debug::log($this.' levels up.');
		$this->level++;
		$this->trains++;
		$this->practices += ceil($this->getWis() / 5);

		if($display) {
			Server::out($this, 'You LEVELED UP!');
			Server::out($this, 'Congratulations, you are now level ' . $this->level . '!');
		}
	}

	public function getLevel()
	{
		return $this->level;
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
	
	public function respawn()
	{
		$this->is_alive = true;
	}

	public function isAlive()
	{
		return $this->is_alive;
	}

	public function addExperience($experience)
	{
		$this->experience += $experience;
	}

	public function applyExperienceFrom(Actor $victim)
	{
		Debug::log("Applying experience from ".$victim." to ".$this.".");
		if($this->experience < $this->experience_per_level) {
			$experience = $victim->getKillExperience($this);
			$this->experience += $experience;
			Server::out($this, "You get ".$experience." experience for your kill.");
		}
	}

	protected function getKillExperience(Actor $killer)
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
	
	public function getExperience()
	{
		return $this->experience;
	}
	
	public function getExperiencePerLevel()
	{
		return $this->experience_per_level; 
	}

	public function consume($item)
	{
		if($this->removeItem($item) !== false) {
			foreach($item->getAffects() as $aff) {
				$aff->apply($this);
			}
		}
	}

	public function applyListeners()
	{
		// all actors get one attack per round to start
		$this->on('attack', function($event, $fighter) {
			$fighter->attack('Reg');
		});

		// return fire if attacked
		$this->on('hit', function($event, $victim, $attacker) {
			if(!$victim->getTarget()) {
				$victim->setTarget($attacker);
			}
		});

		$this->on('mod_hp', function($event, $actor) {
			if($actor->isAlive() && $actor->getAttribute('hp') < 1) {
				$actor->death();
			}
		});

		// regen on tick
		$actor = $this;
		Server::instance()->on('tick', function($event, $server) use ($actor) {
			$actor->tick($event);
		});
	}
	
	public function __toString()
	{
		return $this->alias;
	}
}
?>
