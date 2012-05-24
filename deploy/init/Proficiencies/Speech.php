<?php
namespace Phud\Proficiencies;

class Speech extends Proficiency
{
	protected static $name = 'speech';
	protected static $attributes = ['cha'];
	protected static $base_improvement_chance = 0.015;
	
	public function getImprovementListeners()
	{
		$prof = $this;
		return [
		];
	}
}

Proficiency::register('Speech');
?>
