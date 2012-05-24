<?php
namespace Phud\Proficiencies;

class Healing extends Proficiency
{
	protected static $name = 'healing';
	protected static $attributes = ['wis'];
	protected static $base_improvement_chance = 0.05;
	
	public function getImprovementListeners()
	{
		$prof = $this;
		return [
			['casting', function($event, $actor, $spell) use ($prof) {
				if($spell->getProficiency() === $prof::getName()) {
					$prof->checkImprove();
				}
			}]
		];
	}
}

Proficiency::register('Healing');
?>
