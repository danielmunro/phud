<?php
namespace Phud\Abilities;
use Phud\Actors\Actor,
	Phud\Event;

class Third_Attack extends Skill
{
	protected $alias = 'third attack';
	protected $proficiency = 'melee';
	protected $required_proficiency = 40;
	protected $normal_modifier = ['str'];
	protected $hard_modifier = ['dex'];
	protected $event = Event::MELEE_ATTACK;

	protected function initializeListener()
	{
		$this->listener = function($fighter) {
			$target = $fighter->getTarget();
			if($target && $target->fire(Event::MELEE_ATTACKED) === 'satisfy') {
				return;
			}
			$ability->perform($fighter);
		};
	}

	protected function applyCost(Actor $actor) {}

	protected function success(Actor $actor)
	{
		$actor->attack('3rd');
	}

	protected function fail(Actor $actor)
	{
	}
}
?>
