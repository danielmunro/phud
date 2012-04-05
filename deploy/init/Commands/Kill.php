<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server,
	Phud\Event\Subscriber,
	Phud\Event\Event;

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
		}
	}
}
?>
