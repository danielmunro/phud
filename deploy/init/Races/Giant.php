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

class Giant extends Race
{
	protected $alias = 'giant';
	protected $movement_cost = 2;
	protected $full = 30;
	protected $hunger = 15;
	protected $thirst = 15;
	protected $unarmed_verb = 'pummel';
	protected $size = self::SIZE_GIGANTIC;
	protected $playable = true;
	protected $proficiencies = [
		'one handed weapons' => 10,
		'two handed weapons' => 10,
		'leather armor' => 5,
		'melee' => 10,
		'alchemy' => 10,
		'elemental' => 10
	];

	protected function __construct()
	{
		$this->attributes = new Attributes([
			'str' => 7,
			'int' => -5,
			'wis' => -4,
			'dex' => 0,
			'con' => 4,
			'cha' => -3,
			'dam' => 2
		]);

		parent::__construct();
	}

	public function getSubscribers()
	{
		return [
			new Subscriber(
				Event::EVENT_MOVED,
				function($subscriber, $actor, $movement_cost, $room) {
					$t = $room->getTerrainType();
					if($t === Room::TERRAIN_HILLS ||
						$t === Room::TERRAIN_MOUNTAINS ||
						$t === Room::TERRAIN_GRASSLANDS) {
						$movement_cost /= 2;
					}
				}
			),
			new Subscriber(
				Event::EVENT_DAMAGE_MODIFIER_DEFENDING,
				function($subscriber, $victim, $attacker, $modifier, $dam_roll) {
					$modifier -= 0.05;
				}
			),
			new Subscriber(
				Event::EVENT_CASTED_AT,
				function($subscriber, $target, $caster, $spell, $modifier, $saves) {
					$modifier += 0.08;
				}
			)
		];
	}

	public function getAbilities()
	{
		return [
			'bash',
			'meditation'
		];
	}
}
?>
