<?php
namespace Mechanics\Ability;
use \Living\User,
	\Mechanics\Actor,
	\Mechanics\Event\Subscriber,
	\Mechanics\Event\Event,
	\Mechanics\Server,
	\Exception;

abstract class Skill extends Ability
{
	protected function getInputSubscriber($alias = '')
	{
		if(empty($alias)) {
			$alias = $this->alias;
		}
		return new Subscriber(
			Event::EVENT_INPUT,
			$this,
			function($subscriber, $user, $skill, $args) use ($alias) {
				if(!$subscriber->isBroadcastSatisfied() && strpos($alias, $args[0]) === 0) {
					if($skill->perform($user, $args)) {
						$user->incrementDelay($skill->getDelay());
					}
					$subscriber->satisfyBroadcast();
				}
			},
			true
		);
	}

	protected function determineTarget(Actor $actor, $args)
	{
		return $actor->reconcileTarget($args);
	}
	
	abstract public function getSubscriber();
}
?>
