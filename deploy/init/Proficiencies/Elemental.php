<?php
namespace Phud\Proficiencies;

class Elemental extends Proficiency
{
	protected static $name = 'elemental';
	protected static $attributes = ['int'];
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

Proficiency::register('Elemental');
?>
