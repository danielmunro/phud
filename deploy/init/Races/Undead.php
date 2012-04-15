<?php
namespace Phud\Races;
use Phud\Attributes;

class Undead extends Race
{
	protected $alias = 'undead';
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

	public function getListeners()
	{
		return [
			['casting',
			function($event, $undead, $target, $spell, &$modifier) {
				$p = $spell->getProficiency();
				if($p === 'sorcery' || $p === 'maladictions') {
					$modifier += 0.10;
				}
			}],
			['casted on',
			function($event, $undead, $caster, $spell, &$modifier) {
				$p = $spell->getProficiency();
				if($p === 'sorcery' || $p === 'maladictions') {
					$modifier -= 0.10;
				}
			}]
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
