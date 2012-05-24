<?php
namespace Phud\Proficiencies;

class Two_Handed_Weapons extends Proficiency
{
	protected static $name = 'two handed weapons';
	protected static $attributes = ['str'];
	protected static $base_improvement_chance = 0.005;
	
	public function getImprovementListeners()
	{
		$prof = $this;
		return [
			['attack', function($event, $fighter) use ($prof) {
				$attacking_weapon = $this->getEquipped()->getEquipmentByPosition(Equipment::POSITION_WIELD);
				if($attacking_weapon->getHanded() == 2) {
					$prof->checkImprove();
				}
			}]
		];
	}
}

Proficiency::register('Two_Handed_Weapons');
?>
