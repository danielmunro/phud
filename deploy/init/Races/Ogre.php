<?php
namespace Races;
use \Mechanics\Alias,
	\Mechanics\Race,
	\Mechanics\Effect,
	\Mechanics\Damage,
	\Mechanics\Attributes,
	\Mechanics\Server,
	\Mechanics\Event\Subscriber,
	\Mechanics\Event\Event;

class Ogre extends Race
{
	protected $alias = 'ogre';
	protected $movement_cost = 2;
	protected $full = 2;
	protected $hunger = 4;
	protected $thirst = 2;
	protected $unarmed_verb = 'pummel';
	protected $size = self::SIZE_LARGE;
	protected $playable = true;
	protected $proficiencies = [
		'one handed weapons' => 10,
		'two handed weapons' => 10,
		'chain armor' => 5,
		'plate armor' => 5,
		'melee' => 10,
		'alchemy' => 5,
		'curative' => 5,
		'evasive' => 5
	];

	protected function __construct()
	{
		$this->attributes = new Attributes([
			'str' => 5,
			'int' => -5,
			'wis' => -2,
			'dex' => -1,
			'con' => 3,
			'cha' => -3,
			'dam' => 2,
			'saves' => 1
		]);

		parent::__construct();
	}

	public function getSubscribers()
	{
		return [
			// Small chance at an extra attack
			new Subscriber(
				Event::EVENT_MELEE_ATTACK,
				function($subscriber, $attacker) {
					if(Server::chance() < 5) {
						$attacker->attack('Ogr');
					}
				}
			),
			// Resist fire/frost, vuln magic/mental
			new Subscriber(
				Event::EVENT_DAMAGE_MODIFIER_DEFENDING,
				function($subscriber, $attacker, $victim, &$modifier, &$dam_roll, $attacking) {
					if($attacking && method_exists($attacking, 'getDamageType')) {
						$d = $attacking->getDamageType();
						if($d === Damage::TYPE_FIRE || $d === Damage::TYPE_FROST) {
							$modifier -= 0.15;
						}
						if($d === Damage::TYPE_MAGIC || $d === Damage::TYPE_MENTAL) {
							$modifier += 0.10;
						}
					}
				}
			)
		];
	}

	public function getAbilities()
	{
		return [
			'enhanced damage'
		];
	}
}
?>
