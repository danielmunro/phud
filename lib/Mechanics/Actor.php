<?php
namespace Mechanics;
use \Mechanics\Ability\Ability,
	\Mechanics\Ability\Skill,
	\Mechanics\Event\Subscriber,
	\Mechanics\Event\Broadcaster,
	\Mechanics\Event\Event,
	\Living\User,
	\Items\Corpse,
	\Items\Food;

abstract class Actor
{
	use Affectable, Broadcaster, Inventory, Usable, EasyInit;

	const MAX_LEVEL = 51;
	
	const DISPOSITION_STANDING = 'standing';
	const DISPOSITION_SITTING = 'sitting';
	const DISPOSITION_SLEEPING = 'sleeping';
	
	const SEX_NEUTRAL = 1;
	const SEX_FEMALE = 2;
	const SEX_MALE = 3;

	const MAX_ATTRIBUTE = 25;
	
	protected $alias = '';
	protected $long = '';
	protected $level = 0;
	protected $gold = 0;
	protected $silver = 0;
	protected $copper = 0;
	protected $sex = self::SEX_NEUTRAL;
	protected $disposition = self::DISPOSITION_STANDING;
	protected $race = 'critter';
	protected $room = null;
	protected $equipped = null;
	protected $alignment = 0;
	protected $attributes = null;
	protected $max_attributes = null;
	protected $abilities = [];
	protected $target = null;
	protected $experience = 0;
	protected $experience_per_level = 0;
	protected $_subscriber_delay = null;
	protected $_subscribers_race = [];
	protected $_subscriber_tick = null;
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
		$this->setRace(Race::lookup($this->race));
		$this->equipped = new Equipped($this);
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
		// Remember what abilities the fighter has
		$this->abilities[] = $ability['alias'];
		if($ability['lookup'] instanceof Skill) {
			// Apply the subscriber to trigger the ability at the right time
			$this->addSubscriber($ability['lookup']->getSubscriber());
		}
	}

	public function removeAbility($ability)
	{
		$alias = $ability['alias'];
		if(isset($this->ability[$alias])) {
			unset($this->ability[$alias]);
			if($ability['lookup'] instanceof Skill) {
				$this->removeSubscriber($ability['lookup']->getSubscriber());
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
	}

	public function setAttribute($key, $amount)
	{
		return $this->attributes->setAttribute($key, $amount);
	}

	///////////////////////////////////////////////////////////////////
	// Tick functions
	///////////////////////////////////////////////////////////////////

	public function getSubscriberTick()
	{
		if(!$this->_subscriber_tick) {
			$this->_subscriber_tick = new Subscriber(
				Event::EVENT_TICK,
				$this,
				function($subscriber, $broadcaster, $actor) {
					$actor->tick();
				}
			);
		}
		return $this->_subscriber_tick;
	}

	public function tick()
	{
		if($this->isAlive()) {
			foreach(['hp', 'mana', 'movement'] as $att) {
				$max = $this->getMaxAttribute($att);
				$amount = round(rand($max * 0.05, $max * 0.1));
				$this->modifyAttribute($att, $amount);
			}
		}
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
			Server::instance()->addSubscriber($this->getAttackSubscriber());
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
				if($subscriber->isSuppressed()) {
					$subscriber->suppress(false);
				} else {
					$fighter->attack('Reg');
				}
			}
		);
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
			$victim->setTarget($this);
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
			$dam_roll = _range(0, 200, $dam_roll);
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
			Server::out($a, ($a === $this ? '('.$attack_name.') Your' : ucfirst($this)."'s").' '.$descriptor.' '.$verb.' '.($dam_roll > 0 ? 'hits ' : 'misses ').($victim === $a ? 'you' : $victim) . '.');
		}

		if(!$victim->isAlive()) {
			$victim->afterDeath($this);
		}
	}

	protected function afterDeath($killer)
	{
		$this->setTarget(null);
		$killer->setTarget(null);

		Debug::log(ucfirst($killer).' killed '.$this.".");
		Server::out($killer, 'You have KILLED '.$this.'.');
		$killer->applyExperienceFrom($this);

		if($this instanceof User)
			$nouns = $this->getAlias();
		elseif($this instanceof Mob)
			$nouns = $this->getNouns();

		$gold = round($this->gold / 3);
		$silver = round($this->silver / 3);
		$copper = round($this->copper / 3);

		$killer->modifyCurrency('gold', $gold);
		$killer->modifyCurrency('silver', $silver);
		$killer->modifyCurrency('copper', $copper);

		$this->gold = $gold;
		$this->silver = $silver;
		$this->copper = $copper;

		$this->getRoom()->announce($this, "You hear ".$this."'s death cry.");
		if(chance() < 25) {
			$parts = $this->race['lookup']->getParts();
			$custom_message = [
				['brains' => ucfirst($this)."'s brains splash all over you!"],
				['guts' => ucfirst($this).' spills '.$this->getDisplaySex().' guts all over the floor.'],
				['heart' => ucfirst($this)."'s heart is torn from ".$this->getDisplaySex(). " chest."]
			];
			$k = array_rand($parts);
			if(isset($custom_message[$parts[$k]])) {
				$message = $custom_message[$parts[$k]];
			} else {
				$message = ucfirst($this)."'s ".$parts[$k].' is sliced from '.$this->getDisplaySex().' body.';
			}
			$this->getRoom()->announce([
				['actor' => '*', 'message' => $message]
			]);
			$this->getRoom()->addItem(new Food([
				'short' => 'the '.$parts[$k].' of '.$this,
				'long' => 'The '.$parts[$k].' of '.$this.' is here.',
				'nouns' => $parts[$k].' '.$nouns,
				'nourishment' => 5
			]));
		}
		$corpse = new Corpse([
			'short' => 'a corpse of '.$this,
			'long' => 'A corpse of '.$this.' lies here.',
			'nouns' => 'corpse '.(property_exists($this, 'nouns') ? $this->nouns : $this),
			'weight' => 100,
			'copper' => $copper,
			'silver' => $silver,
			'gold' => $gold
		]);
		foreach($this->items as $i) {
			$this->removeItem($i);
			$corpse->addItem($i);
		}
		$this->getRoom()->addItem($corpse);
		if($killer instanceof User) {
			Server::out($killer, "\n".$killer->prompt(), false);
		}

		$this->handleDeath();
	}

	protected function handleDeath()
	{
		Debug::log(ucfirst($this).' died.');
		Server::out($this, 'You have been KILLED!');
	}

	public function getProficiencyIn($proficiency)
	{
		if(!isset($this->proficiencies[$proficiency])) {
			Debug::log("Error, proficiency not defined: ".$proficiency);
			$this->proficiencies[$proficiency] = 15;
		}
		return $this->proficiencies[$proficiency];
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

	public function getAlias()
	{
		return $this->alias;
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
		if(is_object($this->race['lookup'])) {
			// Undo all previous racial subscribers/abilities/stats/proficiencies
			foreach($this->_subscribers_race as $subscriber) {
				$this->removeSubscriber($subscriber);
			}
			foreach($this->race['lookup']->getProficiencies() as $proficiency => $amount) {
				$this->proficiencies[$proficiency] -= $amount;
			}
			foreach($this->race['lookup']->getAbilities() as $ability_alias) {
				$ability = Ability::lookup($ability_alias);
				$this->removeAbility($ability);
			}
		}

		// Assign all racial subscribers/abilities/stats/proficiencies
		$this->race = $race;
		$this->_subscribers_race = $race['lookup']->getSubscribers();
		foreach($this->_subscribers_race as $subscriber) {
			$this->addSubscriber($subscriber);
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

	public function setLevel($level)
	{
		while($this->level < $level)
		{
			$this->experience += $this->getExperiencePerLevel() - ($this->experience - ($this->level*$this->getExperiencePerLevel()));
			$this->levelUp(false);
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

	public function isAlive()
	{
		return $this->getAttribute('hp') > 0;
	}

	public function applyExperienceFrom(Actor $victim)
	{
		if(!$this->experience_per_level) {
			return 0;
		}

		Debug::log("Applying experience from ".$victim." to ".$this.".");
		if($this->experience < $this->experience_per_level) {
			$experience = $victim->getKillExperience($this);
			$this->experience += $experience;
			Server::out($this, "You get ".$experience." experience for your kill.");
		}
	}

	public function getKillExperience(Actor $killer)
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
	
	public function __toString()
	{
		return $this->alias;
	}
}
?>
