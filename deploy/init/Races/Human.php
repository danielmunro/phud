<?php
namespace Races;
use \Mechanics\Alias,
	\Mechanics\Race,
	\Mechanics\Event\Subscriber,
	\Mechanics\Event\Event,
	\Mechanics\Attributes;

class Human extends Race
{
	protected $alias = 'human';
	protected $playable = true;
	protected $proficiencies = [
		'alchemy' => 10,
		'one handed weapons' => 5,
		'melee' => 5,
		'evasive' => 5,
		'speech' => 5,
		'leather armor' => 5,
		'elemental' => 5,
		'benedictions' => 5,
		'healing' => 5,
		'beguiling' => 5
	];

	protected function __construct()
	{
		$this->attributes = new Attributes();

		parent::__construct();
	}

	public function getSubscribers()
	{
		return [
		];
	}

	public function getAbilities()
	{
		return [
			'haggle'
		];
	}
}
?>
