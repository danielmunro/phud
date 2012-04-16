<?php
namespace Phud\Races;
use Phud\Event\Subscriber,
	Phud\Event\Event,
	Phud\Items\Item,
	Phud\Server,
	Phud\Attributes;

class Elf extends Race
{
	protected $alias = 'elf';
	protected $playable = true;
	protected $proficiencies = [
		'stealth' => 10,
		'one handed weapons' => 5,
		'leather armor' => 5,
		'archery' => 10,
		'elemental' => 5,
		'illusion' => 5,
		'evasive' => 5,
		'speech' => 5,
		'beguiling' => 5
	];

	protected function __construct()
	{
		$this->attributes = new Attributes([
			'str' => -5,
			'int' => 2,
			'wis' => 1,
			'dex' => 3,
			'con' => -1,
			'cha' => 1
		]);

		parent::__construct();
	}
	
	public function getListeners()
	{
		return [
			['defense modifier',
			function($event, $elf, $attacker, &$modifier, &$dam, &$weapon) {
				if(!empty($weapon['equipped']) && $weapon->getMaterial() === 'iron') {
					$modifier += 0.15;
				}
			}],
			['casted on',
			function($event, $elf, $caster, $spell, &$modifier) {
				if($spell['lookup']->getProficiency() === 'beguiling') {
					$modifier -= 0.25;
					if($spell['alias'] === 'sleep') {
						$modifier -= 0.25;
					}
				}
			}],
			['attacked',
			function($event, $elf) {
				if(chance() < 5) {
					$event->satisfy();
					$attacker = $elf->getTarget();
					$elf->getRoom()->announce([
						['actor' => $elf, 'message' => "Your quick reflexes evade ".$attacker."'s attack!"],
						['actor' => $attacker, 'message' => ucfirst($elf)." evades your attack!"],
						['actor' => '*', 'message' => ucfirst($elf)." evades ".$attacker."'s attack!"]
					]);
				}
			}]
		];
	}

	public function getAbilities()
	{
		return [
			'sneak'
		];
	}
}
?>
