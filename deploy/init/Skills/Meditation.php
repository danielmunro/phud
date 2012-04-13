<?php
namespace Phud\Abilities;
use Phud\Event,
	Phud\Actors\Actor;

class Meditation extends Skill
{
	protected $alias = 'meditation';
	protected $proficiency = 'healing';
	protected $required_proficiency = 20;
	protected $easy_modifier = ['wis'];
	protected $event = 'tick';

	protected function initializeListener()
	{
		$skill = $this;
		$this->listener = function($actor, &$amount, &$modifier) use ($skill) {
			$skill->perform($actor, [$amount, $modifier]);
		};
	}

	protected function applyCost(Actor $actor)
	{
	}

	protected function success(Actor $actor, &$args)
	{
		$args[0] += $this->getProficiencyIn($this->proficiency) / 200;
	}

	protected function fail(Actor $actor)
	{
	}
}
?>
