<?php
namespace Phud\Abilities;
use Phud\Event\Subscriber,
	Phud\Event\Event,
	Phud\Actor;

class Meditation extends Skill
{
	protected $alias = 'meditation';
	protected $proficiency = 'healing';
	protected $required_proficiency = 20;
	protected $easy_modifier = ['wis'];

	public function getSubscriber()
	{
		return new Subscriber(
			Event::EVENT_TICK,
			function($subscription, $meditation, $actor) {
				$meditation->perform($actor);
			}
		);
	}

	protected function applyCost(Actor $actor)
	{
	}

	protected function success(Actor $actor)
	{
		$amount = $actor->getProficiencyIn($this->proficiency) / 100;
		$actor->modifyAttribute('hp', $actor->getMaxAttribute('hp') * $amount);
		$actor->modifyAttribute('mana', $actor->getMaxAttribute('mana') * $amount);
		$actor->modifyAttribute('movement', $actor->getMaxAttribute('movement') * $amount);
	}

	protected function fail(Actor $actor)
	{
	}
}
?>
