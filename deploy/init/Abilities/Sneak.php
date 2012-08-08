<?php
namespace Phud\Abilities;
use Phud\Actors\Actor,
	Phud\Affect;

class Sneak extends Skill
{
	protected $alias = 'sneak';
	protected $proficiency = 'stealth';
	protected $required_proficiency = 30;
	protected $normal_modifier = ['dex'];
	protected $delay = 1;
	protected $event = 'input';

	protected function initializeListener()
	{
		$this->listener = $this->getInputListener();
	}

	protected function applyCost(Actor $actor)
	{
		$m = $actor->getAttribute('movement');
		$cost = -(round((0.05/min(1, $actor->getLevel()/10))*$m));
		$actor->modifyAttribute('movement', $cost);
	}

	protected function success(Actor $actor)
	{
		$a = new Affect([
			'affect' => 'sneak',
			'message_affect' => 'Affect: sneak',
			'message_end' => 'You no longer move silently.',
			'timeout' => min($actor->getAttribute('dex') * 2, $actor->getLevel()),
			'apply' => $actor
		]);
		$actor->getRoom()->announce([
			['actor' => $actor, 'message' => 'You begin to move silently.'],
			['actor' => '*', 'message' => $actor.' fades into the shadows.']
		]);
	}
	
	protected function fail(Actor $actor)
	{
		$actor->notify("Your attempt to move undetected fails.");
	}
}
