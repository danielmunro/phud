<?php
namespace Skills;
use \Mechanics\Ability\Skill,
	\Mechanics\Alias,
	\Mechanics\Actor,
	\Mechanics\Server;

class Backstab extends Skill
{
	protected $alias = 'backstab';
	protected $proficiency = 'stealth';
	protected $required_proficiency = 20;
	protected $hard_modifier = ['dex'];
	protected $needs_target = true;
	protected $is_offensive = true;

	public function getSubscriber()
	{
		return $this->getInputSubscriber();
	}

	protected function applyCost(Actor $actor)
	{
		$actor->incrementDelay(2);
	}

	protected function success(Actor $actor)
	{
		$actor->attack('bks');
	}

	protected function fail(Actor $actor)
	{
		$actor->getRoom()->announce2([
			['actor' => $actor, 'message' => "You fumble your backstab."],
			['actor' => $actor->getTarget(), 'message' => ucfirst($actor)." tries to backstab you but fumbles."],
			['actor' => '*', 'message' => ucfirst($actor)." tries to backstab ".$actor->getTarget()." but fumbles."]
		]);
	}
}
?>
