<?php
namespace Phud\Proficiencies;

class Transportation extends Proficiency
{
	protected static $name = 'transportation';
	protected static $attributes = ['int'];
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

Proficiency::register('Transportation');
?>
