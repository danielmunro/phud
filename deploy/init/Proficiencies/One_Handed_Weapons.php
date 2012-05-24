<?php
namespace Phud\Proficiencies;

class One_Handed_Weapons extends Proficiency
{
	protected static $name = 'one handed weapons';
	protected static $attributes = ['str'];
	protected static $base_improvement_chance = 0.005;
	
	public function getImprovementListeners()
	{
		$prof = $this;
		return [
			['attack', function($event, $fighter) use ($prof) {
				$attacking_weapon = $this->getEquipped()->getEquipmentByPosition(Equipment::POSITION_WIELD);
				if($attacking_weapon->getHanded() == 1) {
					$prof->checkImprove();
				}
			}]
		];
	}
}

Proficiency::register('One_Handed_Weapons');
?>
