<?php
namespace Phud\Proficiencies;

class Plate_Armor extends Proficiency
{
	protected static $name = 'plate armor';
	protected static $attributes = ['str'];
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

Proficiency::register('Plate_Armor');
?>
