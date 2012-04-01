<?php
namespace Skills;
use Phud\Ability\Skill,
	Phud\Ability\Ability,
	\Mechanics\Event\Subscriber,
	\Mechanics\Event\Event,
	\Mechanics\Server,
	\Mechanics\Actor;

class Haggle extends Skill
{
	protected $alias = 'haggle';
	protected $proficiency = 'speech';
	protected $required_proficiency = 20;
	protected $easy_modifier = ['cha'];

	public function getSubscriber()
	{
		return new Subscriber(
			Event::EVENT_BUY,
			function($subscriber, $haggle, $buy_event) {
				//aw fuck I'm at a loss
			}
		);
	}

	protected function applyCost(Actor $actor)
	{
	}

	protected function success(Actor $actor)
	{
	}
	
	protected function fail(Actor $actor)
	{
	}
}
?>
