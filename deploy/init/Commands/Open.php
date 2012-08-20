<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Door;

class Open extends Command
{
	protected $alias = 'open';
	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor, Door $door)
	{
		switch($door->getDisposition()) {
			case mDoor::DISPOSITION_CLOSED:
				$door->setDisposition(mDoor::DISPOSITION_OPEN);
				return $actor->notify('You open '.$door.'.');
			case mDoor::DISPOSITION_OPEN:
				return $actor->notify(ucfirst($door).' is already open.');
			case mDoor::DISPOSITION_LOCKED:
				return $actor->notify(ucfirst($door).' is locked.');
		}
	}

	protected function getArgumentsFromHints(Actor $actor, $args)
	{
		return [(new Arguments\Door())->parse($actor, $args[1])];
	}
}
