<?php
namespace Phud\Abilities;
use Phud\Actors\Actor;

class Meditation extends Skill
{
	protected $alias = 'meditation';
	protected $proficiency = 'healing';
	protected $required_proficiency = 20;
	protected $easy_modifier = ['wis'];
	protected $event = 'tick';

	protected function initializeListener()
	{
		$this->listener = function($event, $actor, &$amount, &$modifier) {
			$this->perform($actor, $actor);
		};
	}

	protected function applyCost(Actor $actor)
	{
	}

	protected function success(Actor $actor, Actor $target, &$args)
	{
		$args[0] += $actor->getProficiencyScore($this->proficiency) / 200;
	}

	protected function fail(Actor $actor)
	{
	}
}
