<?php
namespace Phud\Races;
use Phud\Attributes;

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
	
	public function getListeners()
	{
		return [
			['bash',
			function($event, $target, &$roll) {
				$roll -= 10;
			}]
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
