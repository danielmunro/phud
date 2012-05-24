<?php
namespace Phud\Proficiencies;

class Chain_Armor extends Proficiency
{
	protected static $name = 'chain armor';
	protected static $attributes = ['dex', 'str'];
	protected static $base_improvement_chance = 0.005;
	
	public function getImprovementListeners()
	{
		$prof = $this;
		return [
			['attacked', function($event, $defender) use ($prof) {
			}]
		];
	}
}

Proficiency::register('Chain_Armor');
?>
