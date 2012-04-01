<?php
namespace Phud\Ability;
use Phud\Event\Subscriber,
	Phud\Event\Event;

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
			function($subscriber, $client, $skill, $args) use ($alias) {
				if(!$subscriber->isBroadcastSatisfied() && strpos($alias, $args[0]) === 0) {
					$user = $client->getUser();
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
