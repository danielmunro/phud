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
	
	public function getSubscribers()
	{
		return [
			new Subscriber(
				Event::EVENT_DAMAGE_MODIFIER_DEFENDING,
				function($subscriber, $broadcaster, $victim, &$modifier, &$dam_roll, $attacking_weapon) {
					if($attacking_weapon instanceof Item && $attacking_weapon->getMaterial() === Item::MATERIAL_IRON) {
						$modifier += 0.15;
					}
				}
			),
			new Subscriber(
				Event::EVENT_CASTED_AT,
				function($subscriber, $broadcaster, $target, $spell, &$modifier, &$saves) {
					if($spell['lookup']->getProficiency() === 'beguiling') {
						$modifier -= 0.25;
						if($spell['alias'] === 'sleep') {
							$modifier -= 0.25;
						}
					}
				}
			),
			new Subscriber(
				Event::EVENT_MELEE_ATTACKED,
				function($subscriber, $target, $attack_subscriber) {
					if(chance() < 5) {
						$attack_subscriber->suppress();
						Server::out($target, "Your quick reflexes evade ".$target->getTarget()."'s attack!");
						$target->getRoom()->announce($target, ucfirst($target)." evades ".$target->getTarget()."'s attack!");
						$attack_subscriber->satisfyBroadcast();
					}
				}
			)
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
