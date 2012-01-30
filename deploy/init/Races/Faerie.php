<?php
namespace Races;
use \Mechanics\Alias,
	\Mechanics\Race,
	\Mechanics\Attributes;

class Faerie extends Race
{
	protected $alias = 'faerie';
	protected $unarmed_verb = 'slap';
	protected $size = self::SIZE_TINY;
	protected $playable = true;
	protected $thirst = 30;
	protected $hunger = 30;
	protected $full = 60;
	protected $proficiencies = [
		'healing' => 5,
		'alchemy' => 5,
		'elemental' => 10,
		'illusion' => 5,
		'transportation' => 5,
		'sorcery' => 10,
		'maladictions' => 5,
		'benedictions' => 5,
		'curative' => 5
	];

	protected function __construct()
	{
		$this->attributes = new Attributes([
			'str' => -7,
			'int' => 5,
			'wis' => 5,
			'dex' => 5,
			'con' => -7,
			'cha' => 3,
			'hit' => -1,
			'dam' => -1
		]);
		
		parent::__construct();
	}

	protected function setPartsFromForm()
	{
		parent::setPartsFromForm();
		$this->addParts(['wings']);
	}
	
	public function getSubscribers()
	{
		return [
			new Subscriber(
				Event::EVENT_CASTING,
				function($subscriber, $caster, $target, $spell, &$modifier, &$saves) {
					$plus_mod = rand(0.01, 0.08);
					$plus_saves = rand(1, 8);
					$modifier += $plus_mod;
					$saves += $plus_saves;
				}
			),
			new Subscriber(
				Event::EVENT_DAMAGE_MODIFIER_DEFENDING,
				function($subscriber, $victim, $attacker, &$modifier, &$dam_roll, $attacking) {
					$modifier += 0.05;
					if($attacking->getDamageType() === Damage::TYPE_POUND) {
						$modifier += 0.10;
					}
				}
			)
		];
	}

	public function getAbilities()
	{
		return [
			'fly',
			'meditation'
		];
	}
}
?>
