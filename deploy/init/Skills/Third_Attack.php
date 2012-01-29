<?php
namespace Skills;
use \Mechanics\Ability\Ability,
	\Mechanics\Ability\Skill,
	\Mechanics\Actor,
	\Mechanics\Server;

class Third_Attack extends Skill
{
	protected $alias = 'third attack';
	protected $proficiency = 'melee';
	protected $required_proficiency = 40;
	protected $hard_modifier = ['dex'];

	public function getSubscriber()
	{
		return new Subscriber(
			Event::EVENT_MELEE_ATTACK,
			$this,
			function($attack_subscriber, $fighter, $ability) {
				$fighter->getTarget()->fire(Event::EVENT_MELEE_ATTACKED, $attack_subscriber);
				if(!$attack_subscriber->isSuppressed()) {
					$ability->perform($fighter);
				}
			}
		);
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
