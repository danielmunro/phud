<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Door;

class Close extends Command
{
	protected $alias = 'close';
	protected $dispositions = [Actor::DISPOSITION_STANDING];
	protected $min_argument_count = 1;
	protected $min_argument_fail = "Close what?";

	public function perform(Actor $actor, Door $door)
	{
		switch($door->getDisposition()) {
			case Door::DISPOSITION_OPEN:
				$door->setDisposition(Door::DISPOSITION_CLOSED);
				return $actor->notify('You close '.$door.'.');
			case Door::DISPOSITION_CLOSED:
			case Door::DISPOSITION_LOCKED:
				return $actor->notify(ucfirst($door).' is already closed.');
		}					
	}

	protected function getArgumentsFromHints(Actor $actor, $args)
	{
		return [(new Arguments\Door())->parse($actor, $args[1])];
	}
}
