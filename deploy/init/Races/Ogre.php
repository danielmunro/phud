<?php
namespace Phud\Races;
use Phud\Effect,
	Phud\Damage,
	Phud\Attributes,
	Phud\Server;

class Ogre extends Race
{
	protected $alias = 'ogre';
	protected $movement_cost = 2;
	protected $full = 30;
	protected $hunger = 15;
	protected $thirst = 15;
	protected $unarmed_verb = 'pummel';
	protected $size = self::SIZE_LARGE;
	protected $playable = true;
	protected $proficiencies = [
		'one handed weapons' => 10,
		'two handed weapons' => 10,
		'chain armor' => 10,
		'plate armor' => 5,
		'melee' => 10,
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

	public function getListeners()
	{
		return [
			// Small chance at an extra attack
			['attack',
			function($event, $attacker) {
				if(chance() < 0.025) {
					$attacker->attack('Ogr');
				}
			}],
			// Resist fire/frost, vuln magic/mental
			['damage modifier',
			function($event, $attacker, $victim, &$modifier, &$dam_roll, $weapon) {
				if($weapon) {
					$d = $weapon->getDamageType();
					if($d === Damage::TYPE_FIRE || $d === Damage::TYPE_FROST) {
						$modifier -= 0.15;
					}
					if($d === Damage::TYPE_MAGIC || $d === Damage::TYPE_MENTAL) {
						$modifier += 0.10;
					}
				}
			}]
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
