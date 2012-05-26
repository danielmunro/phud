<?php
namespace Phud\Proficiencies;
use Phud\Items\Equipment;

class Two_Handed_Weapons extends Proficiency
{
	protected static $name = 'two handed weapons';
	protected static $attributes = ['str'];
	protected static $base_improvement_chance = 0.005;
	
	public function getImprovementListeners()
	{
		return [
			['attack', function($event, $fighter) {
				$weapon = $fighter->getEquipped()->getEquipmentByPosition(Equipment::POSITION_WIELD);
				if($weapon['equipped'] && $weapon['equipped']->getHanded() == 2) {
					$this->checkImprove($fighter);
				}
			}]
		];
	}
}

Proficiency::register('Two_Handed_Weapons');
?>
