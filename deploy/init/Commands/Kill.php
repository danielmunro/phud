<?php
namespace Phud\Commands;
use Phud\Actors\Actor;

class Kill extends Command
{
	protected $alias = 'kill';
	protected $min_argument_count = 1;
	protected $min_argument_fail = "Kill what?";
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, Actor $target)
	{
		if(!$actor->reconcileTarget($target)) {
			return;
		}

		$event = $actor->getTarget()->fire('attacked', $actor);
		if($event && $event->getStatus() === 'on') {
			$actor->notify("You scream and attack!");
		}
	}

	protected function getArgumentsFromHints($actor, $args)
	{
		return [(new Arguments\Actor())->parse($actor, $args[1])];
	}
}
