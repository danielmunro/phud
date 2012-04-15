<?php
namespace Phud\Races;
use Phud\Attributes;

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
	
	public function getListeners()
	{
		return [
			['casting',
			function($event, $volare, $target, $spell, &$modifier) {
				if($spell->getProficiency() === 'healing') {
					$modifier += 0.1;
				}
			}]
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
