<?php
namespace Phud\Proficiencies;

class Melee extends Proficiency
{
	protected static $name = 'melee';
	protected static $attributes = ['str', 'dex'];
	
	public function getImprovementListeners()
	{
		$prof = $this;
		return [
			['attack', function($event, $fighter) use ($prof) {
				$prof->checkImprove();
			}]
		];
	}
}

Proficiency::register('Melee');
?>
