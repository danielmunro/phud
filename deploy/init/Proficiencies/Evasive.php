<?php
namespace Phud\Proficiencies;

class Evasive extends Proficiency
{
	protected static $name = 'evasive';
	protected static $attributes = ['dex'];
	protected static $base_improvement_chance = 0.005;
	
	public function getImprovementListeners()
	{
		$prof = $this;
		return [
			['evaded', function($event, $actor) use ($prof) {
				$prof->checkImprove($actor);
			}]
		];
	}
}

Proficiency::register('Evasive');
?>
