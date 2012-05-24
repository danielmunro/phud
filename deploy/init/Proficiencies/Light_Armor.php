<?php
namespace Phud\Proficiencies;

class Light_Armor extends Proficiency
{
	protected static $name = 'light armor';
	protected static $attributes = ['dex'];
	protected static $base_improvement_chance = 0.005;
	
	public function getImprovementListeners()
	{
		$prof = $this;
		return [
			['attacked', function($event, $defender) use ($prof) {
				/**
				$eq = $defender->getEquipped()->getEquipmentByPosition(Equipment::POSITION_TORSO);
				if($eq && $e) {
					$prof->checkImprove();
				}
				*/
			}]
		];
	}
}

Proficiency::register('Light_Armor');
?>
