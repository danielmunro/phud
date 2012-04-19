<?php
namespace Phud\Races;
use Phud\Alias,
	Phud\Attributes;

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
	
	public function getListeners()
	{
		return [
			['casting',
			function($event, $caster, $target, $spell, &$modifier) {
				$modifier += rand(0.01, 0.08);
			}],
			['defense modifier',
			function($event, $faerie, $attacker, &$modifier, &$dam, $weapon) {
				$modifier += 0.05;
				if($weapon->getDamageType() === Damage::TYPE_POUND) {
					$modifier += 0.1;
				}
			}]
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
