<?php
namespace Races;
use Phud\Race,
	Phud\Event\Subscriber,
	Phud\Event\Event,
	Phud\Items\Item,
	Phud\Server,
	Phud\Attributes;

class Gnome extends Race
{
	protected $alias = 'gnome';
	protected $playable = true;
	protected $proficiencies = [
		'stealth' => 15,
		'one handed weapons' => 5,
		'leather armor' => 5,
		'alchemy' => 10,
		'illusion' => 10,
		'evasive' => 5,
		'speech' => 10
	];

	protected function __construct()
	{
		$this->attributes = new Attributes([
			'str' => -2,
			'int' => 0,
			'wis' => 0,
			'dex' => 5,
			'con' => -3,
			'cha' => 1,
			'saves' => 2,
			'movement' => 50
		]);

		parent::__construct();
	}
	
	public function getSubscribers()
	{
		return [
			new Subscriber(
				Event::EVENT_BASHED,
				function($subscriber, $target, $roll) {
					$roll -= 10;
				}
			)
		];
	}

	public function getAbilities()
	{
		return [
			'sneak',
			'haggle'
		];
	}
}
?>
