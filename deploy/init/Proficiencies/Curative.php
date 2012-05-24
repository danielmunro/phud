<?php
namespace Phud\Proficiencies;

class Curative extends Proficiency
{
	protected static $name = 'curative';
	protected static $attributes = ['wis'];
	protected static $base_improvement_chance = 0.01;
	
	public function getImprovementListeners()
	{
		$prof = $this;
		return [
			['casting', function($event, $actor, $spell) use ($prof) {
				if($spell->getProficiency() === $prof::getName()) {
					$prof->checkImprove($actor);
				}
			}]
		];
	}
}

Proficiency::register('Curative');
?>
