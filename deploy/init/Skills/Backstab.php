<?php
namespace Skills;
use Phud\Ability\Skill,
	Phud\Actor\Actor;

class Backstab extends Skill
{
	protected $alias = 'backstab';
	protected $proficiency = 'stealth';
	protected $required_proficiency = 20;
	protected $hard_modifier = ['dex'];
	protected $needs_target = true;
	protected $is_offensive = true;
	protected $delay = 2;

	public function getSubscriber()
	{
		return $this->getInputSubscriber();
	}

	protected function applyCost(Actor $actor)
	{
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
