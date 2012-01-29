<?php
namespace Races;
use \Mechanics\Alias,
	\Mechanics\Race,
	\Mechanics\Event\Subscriber,
	\Mechanics\Event\Event,
	\Mechanics\Item,
	\Mechanics\Server,
	\Mechanics\Attributes;

class Volare extends Race
{
	protected $alias = 'volare';
	protected $playable = true;
	protected $proficiencies = [
		'healing' => 10,
		'benedictions' => 10,
		'curative' => 10,
		'one handed weapons' => 5,
		'leather armor' => 5,
		'speech' => 5
	];

	protected function __construct()
	{
		$this->attributes = new Attributes([
			'str' => -5,
			'int' => 4,
			'wis' => 4,
			'dex' => 1,
			'con' => -4,
			'cha' => 2
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
				Event::EVENT_HEALING,
				function($subscriber, $caster, $target, $spell, $modifier, $saves) {
					$modifier += 0.10;
				}
			)
		];
	}

	public function getAbilities()
	{
		return [
			'cure light',
			'armor',
			'meditation'
		];
	}
}
?>
