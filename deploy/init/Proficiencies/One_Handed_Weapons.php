<?php
namespace Phud\Proficiencies;
use Phud\Items\Equipment;

class One_Handed_Weapons extends Proficiency
{
	protected static $name = 'one handed weapons';
	protected static $attributes = ['str'];
	protected static $base_improvement_chance = 0.005;
	
	public function getImprovementListeners()
	{
		return [
			['attack', function($event, $fighter) {
				$weapon = $fighter->getEquipped()->getEquipmentByPosition(Equipment::POSITION_WIELD);
				if($weapon['equipped'] && $weapon['equipped']->getHanded() == 1) {
					$this->checkImprove($fighter);
				}
			}]
		];
	}
}

Proficiency::register('One_Handed_Weapons');
?>
