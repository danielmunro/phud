<?php
namespace Skills;
use \Mechanics\Ability\Skill,
	\Mechanics\Ability\Ability,
	\Mechanics\Server,
	\Mechanics\Actor;

class Second_Attack extends Skill
{
	protected $alias = 'second attack';
	protected $proficiency = 'melee';
	protected $required_proficiency = 30;
	protected $normal_modifier = ['dex', 'str'];

	public function getSubscriber()
	{
		return new Subscriber(
			Event::EVENT_MELEE_ATTACK,
			$this,
			function($attack_subscriber, $fighter, $ability) {
				if(!$attack_subscriber->isSuppressed()) {
					$ability->perform($fighter);
				}
			}
		);
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
