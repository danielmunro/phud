<?php
namespace Phud\Races;
use Phud\Attributes;

class Kobold extends Race
{
	protected $alias = 'kobold';

	protected function __construct()
	{
		$this->attributes = new Attributes([
			'str' => -3,
			'int' => 1,
			'wis' => 0,
			'dex' => 3,
			'con' => 0,
			'cha' => 0
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
			}]
		];
	}

	public function getAbilities()
	{
		return ['sneak'];
	}
}
?>
