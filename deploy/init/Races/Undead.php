<?php
namespace Races;
use \Mechanics\Alias,
	\Mechanics\Race,
	\Mechanics\Attributes,
	\Mechanics\Event\Event,
	\Mechanics\Event\Subscriber;

class Undead extends Race
{
	protected $alias = 'undead';
	protected $full = 5;
	protected $hunger = 3;
	protected $movement_cost = 2;
	protected $unarmed_verb = 'swipe';
	protected $move_verb = 'limps';
	protected $playable = true;
	protected $proficiencies = [
		'one handed weapons' => 5,
		'two handed weapons' => 5,
		'melee' => 10,
		'sorcery' => 10,
		'maladictions' => 10,
		'transportation' => 5,
		'illusion' => 5,
		'stealth' => 5
	];

	protected function __construct()
	{
		$this->attributes = new Attributes([
			'str' => 3,
			'int' => 3,
			'wis' => -2,
			'dex' => -2,
			'con' => -1,
			'cha' => -5,
			'hit' => 1,
			'saves' => -10
		]);
		
		parent::__construct();
	}

	public function getSubscribers()
	{
		return [
			new Subscriber(
				Event::EVENT_CASTING,
				function($subscriber, $caster, $target, $spell, &$modifier, &$saves) {
					$p = $spell->getProficiency();
					if($p === 'sorcery' || $p === 'maladictions') {
						$modifier += 0.10;
					}
				}
			),
			new Subscriber(
				Event::EVENT_CASTED_AT,
				function($subscriber, $target, $caster, $spell, &$modifier, &$saves) {
					$p = $spell->getProficiency();
					if($p === 'sorcery' || $p === 'maladictions') {
						$modifier -= 0.10;
					}
				}
			)
		];
	}

	public function getAbilities()
	{
		return [
			'fear'
		];
	}
}
?>
