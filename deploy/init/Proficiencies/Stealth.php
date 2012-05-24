<?php
namespace Phud\Proficiencies;

class Stealth extends Proficiency
{
	protected static $name = 'stealth';
	protected static $attributes = ['dex'];
	protected static $base_improvement_chance = 0.005;
	
	public function getImprovementListeners()
	{
		$prof = $this;
		return [
			['moved', function($event, $actor) use ($prof) {
				if($actor->isAffectedBy('sneak')) {
					$prof->checkImprove();
				}
			}]
		];
	}
}

Proficiency::register('Stealth');
?>
