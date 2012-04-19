<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Server;

class Kill extends Command
{
	protected $alias = 'kill';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, $args = [])
	{
		if(!$actor->reconcileTarget($args)) {
			return;
		}

		$event = $actor->getTarget()->fire('attacked', $actor);
		if($event && $event->getStatus() === 'on') {
			Server::out($actor, "You scream and attack!");
		}
	}
}
?>
