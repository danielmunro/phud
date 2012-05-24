<?php
namespace Phud\Proficiencies;

class Melee extends Proficiency
{
	protected static $name = 'melee';
	protected static $attributes = ['str', 'dex'];
	protected static $base_improvement_chance = 0.005;
	
	public function getImprovementListeners()
	{
		$prof = $this;
		return [
			['attack', function($event, $fighter) use ($prof) {
				$prof->checkImprove($fighter);
			}]
		];
	}
}

Proficiency::register('Melee');
?>
