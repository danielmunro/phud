<?php
namespace Skills;
use \Mechanics\Ability\Skill,
	\Mechanics\Actor,
	\Mechanics\Server,
	\Mechanics\Affect;

class Sneak extends Skill
{
	protected $alias = 'sneak';
	protected $proficiency = 'stealth';
	protected $required_proficiency = 30;
	protected $normal_modifier = ['dex'];
	protected $delay = 1;

	public function getSubscriber()
	{
		return $this->getInputSubscriber();
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
		$actor->getRoom()->announce2([
			['actor' => $actor, 'message' => 'You begin to move silently.'],
			['actor' => '*', 'message' => $actor.' fades into the shadows.']
		]);
	}
	
	protected function fail(Actor $actor)
	{
		Server::out($actor, "Your attempt to move undetected fails.");
	}
}
?>
