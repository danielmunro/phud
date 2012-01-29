<?php
namespace Commands;
use \Mechanics\Actor,
	\Mechanics\Alias,
	\Mechanics\Server,
	\Mechanics\Event\Subscriber,
	\Mechanics\Event\Event,
	\Mechanics\Command\Command;

class Kill extends Command
{
	protected $alias = 'kill';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [], Subscriber $command_subscriber)
	{
		if(!$actor->reconcileTarget($args)) {
			return;
		}

		$actor->getTarget()->fire(Event::EVENT_ATTACKED, $actor, $command_subscriber);
		if(!$command_subscriber->isSuppressed()) {
			Server::out($actor, "You scream and attack!");
			Server::instance()->addSubscriber($actor->getAttackSubscriber());
		}
	}
}
?>
