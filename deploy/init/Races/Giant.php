<?php
namespace Phud\Races;
use Phud\Effect,
	Phud\Damage,
	Phud\Attributes,
	Phud\Server;

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

	public function getListeners()
	{
		return [
			['moved',
			function($event, $actor, &$movement_cost, $room) {
				$t = $room->getTerrainType();
				if($t === Room::TERRAIN_HILLS ||
					$t === Room::TERRAIN_MOUNTAINS ||
					$t === Room::TERRAIN_GRASSLANDS) {
					$movement_cost /= 2;
				}
			}],
			['defense modifier',
			function($event, $giant, $attacker, &$modifier) {
				$modifier -= 0.05;
			}],
			['casted on',
			function($event, $giant, $caster, $spell, &$modifier) {
				if($spell->isOffensive()) {
					$modifier += 0.08;
				}
			}]
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
