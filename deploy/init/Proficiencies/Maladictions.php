<?php
namespace Phud\Proficiencies;

class Maladictions extends Proficiency
{
	protected static $name = 'maladictions';
	protected static $attributes = ['int', 'wis'];
	protected static $base_improvement_chance = 0.01;
	
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

Proficiency::register('Maladictions');
?>
