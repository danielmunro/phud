<?php
namespace Phud\Commands;
use Phud\Actors\Actor,
	Phud\Room\Room;

class Recall extends Command
{
	protected $alias = 'recall';

	protected $dispositions = [Actor::DISPOSITION_STANDING];

	public function perform(Actor $actor)
	{
		$actor->setRoom(Room::getByID(1));
		Command::lookup('look')->perform($actor);
	}
}
