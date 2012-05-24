<?php
namespace Phud\Proficiencies;

class Beguiling extends Proficiency
{
	protected static $name = 'beguiling';
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

Proficiency::register('Beguiling');
?>
