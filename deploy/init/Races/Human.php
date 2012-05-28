<?php
namespace Phud\Races;
use Phud\Attributes;

class Human extends Race
{
	protected $alias = 'human';
	protected $playable = true;
	protected $proficiencies = [
		'one handed weapons' => 10,
		'melee' => 5,
		'evasive' => 5,
		'speech' => 5,
		'light armor' => 10,
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

	public function getListeners()
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
