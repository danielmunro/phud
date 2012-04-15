<?php
namespace Phud\Abilities;
use Phud\Actors\Actor;

class Second_Attack extends Skill
{
	protected $alias = 'second attack';
	protected $proficiency = 'melee';
	protected $required_proficiency = 30;
	protected $normal_modifier = ['dex', 'str'];
	protected $event = 'attack';

	protected function initializeListener()
	{
		$this->listener = function($fighter) {
			$target = $fighter->getTarget();
			if($target && $target->fire('attacked') === 'satisfy') {
				return;
			}
			$ability->perform($fighter);
		};
	}

	protected function applyCost(Actor $actor) {}

	protected function success(Actor $actor)
	{
		$actor->attack('2nd');
	}

	protected function fail(Actor $actor)
	{
	}
}

?>
