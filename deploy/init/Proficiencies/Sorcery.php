<?php
namespace Phud\Proficiencies;

class Sorcery extends Proficiency
{
	protected static $name = 'sorcery';
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

Proficiency::register('Sorcery');
?>
